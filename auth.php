<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/bootstrap.php';

nova_session_start();
header('Content-Type: application/json; charset=utf-8');

function authResponse(bool $success, string $message, array $extra = []): never {
  http_response_code($success ? 200 : 400);
  echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
  exit;
}

/* ─── Brute-force throttle (per email, per session, 10 min window) ── */
const NOVA_LOGIN_MAX_ATTEMPTS = 5;
const NOVA_LOGIN_WINDOW = 600;

function loginAttempts(): array {
  if (!isset($_SESSION['nova_login_attempts']) || !is_array($_SESSION['nova_login_attempts'])) {
    $_SESSION['nova_login_attempts'] = [];
  }
  return $_SESSION['nova_login_attempts'];
}

function registerFailedLogin(string $email): void {
  $attempts = loginAttempts();
  $now = time();
  $bucket = $attempts[$email] ?? ['count' => 0, 'started' => $now];
  if ($now - (int) $bucket['started'] > NOVA_LOGIN_WINDOW) {
    $bucket = ['count' => 0, 'started' => $now];
  }
  $bucket['count']++;
  $_SESSION['nova_login_attempts'][$email] = $bucket;
  sleep(1); // slow credential-stuffing
}

function loginThrottled(string $email): bool {
  $attempts = loginAttempts();
  $bucket = $attempts[$email] ?? null;
  if (!$bucket) return false;
  if (time() - (int) $bucket['started'] > NOVA_LOGIN_WINDOW) {
    unset($_SESSION['nova_login_attempts'][$email]);
    return false;
  }
  return (int) $bucket['count'] >= NOVA_LOGIN_MAX_ATTEMPTS;
}

function clearLoginAttempts(string $email): void {
  unset($_SESSION['nova_login_attempts'][$email]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
  $getAct = strtolower(trim((string)$_GET['action']));
  if (in_array($getAct, ['check', 'status', 'user', 'session'], true)) {
    authResponse(true, 'Session status', [
      'authenticated' => !empty($_SESSION['nova_user']),
      'user' => $_SESSION['nova_user'] ?? null
    ]);
  }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  authResponse(false, 'Unsupported request method.');
}

$payload = json_decode(file_get_contents('php://input'), true);
$payload = is_array($payload) ? $payload : $_POST;
$action = strtolower(trim((string) ($payload['action'] ?? '')));
$email = strtolower(trim((string) ($payload['email'] ?? '')));
$password = (string) ($payload['password'] ?? '');

try {
  $db = novaDb();

  if (in_array($action, ['check', 'status', 'user', 'session'], true)) {
    authResponse(true, 'Session status', [
      'authenticated' => !empty($_SESSION['nova_user']),
      'user' => $_SESSION['nova_user'] ?? null
    ]);
  }

  if ($action === 'register') {
    $name = trim((string) ($payload['name'] ?? ''));
    $confirmPassword = (string) ($payload['confirm_password'] ?? '');

    if ($name === '' || mb_strlen($name) > 120) {
      authResponse(false, 'Enter a valid full name.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      authResponse(false, 'Enter a valid email address.');
    }
    if (strlen($password) < 6) {
      authResponse(false, 'Password must be at least 6 characters.');
    }
    if (strlen($password) > 72) {
      authResponse(false, 'Password must be 72 characters or fewer.');
    }
    if ($password !== $confirmPassword) {
      authResponse(false, 'Passwords do not match.');
    }

    $check = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
      authResponse(false, 'An account with this email already exists.');
    }

    $insert = $db->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
    $insert->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    session_regenerate_id(true);
    $_SESSION['nova_user'] = ['id' => (int) $db->lastInsertId(), 'name' => $name, 'email' => $email];
    authResponse(true, 'Account created successfully.', ['user' => $_SESSION['nova_user']]);
  }

  if ($action === 'login') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
      authResponse(false, 'Enter your email and password.');
    }
    if (loginThrottled($email)) {
      authResponse(false, 'Too many attempts. Please wait 10 minutes and try again.');
    }

    $query = $db->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $query->execute([$email]);
    $user = $query->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
      registerFailedLogin($email);
      authResponse(false, 'Incorrect email or password.');
    }

    clearLoginAttempts($email);
    session_regenerate_id(true);
    $_SESSION['nova_user'] = ['id' => (int) $user['id'], 'name' => $user['name'], 'email' => $user['email']];
    authResponse(true, 'Signed in successfully.', ['user' => $_SESSION['nova_user']]);
  }

  if ($action === 'reset_password') {
    $newPassword = (string) ($payload['new_password'] ?? '');
    $newPasswordConfirm = (string) ($payload['new_password_confirm'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      authResponse(false, 'Enter a valid email address.');
    }
    if (strlen($newPassword) < 6) {
      authResponse(false, 'New password must be at least 6 characters.');
    }
    if (strlen($newPassword) > 72) {
      authResponse(false, 'New password must be 72 characters or fewer.');
    }
    if ($newPassword !== $newPasswordConfirm) {
      authResponse(false, 'New passwords do not match.');
    }

    /* SECURITY: resetting a password requires proving ownership of the
       account — the current password must be provided and verified. */
    if ($password === '') {
      authResponse(false, 'Enter your current password to reset it.');
    }

    $query = $db->prepare('SELECT id, password_hash FROM users WHERE email = ? LIMIT 1');
    $query->execute([$email]);
    $user = $query->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
      registerFailedLogin($email);
      authResponse(false, 'Current password is incorrect.');
    }

    $update = $db->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $email]);
    clearLoginAttempts($email);
    authResponse(true, 'Password updated successfully. You can now sign in.');
  }

  if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    authResponse(true, 'Signed out successfully.');
  }

  authResponse(false, 'Unknown authentication action.');
} catch (Throwable $error) {
  error_log('NOVA auth error: ' . $error->getMessage());
  authResponse(false, 'DEBUG: ' . $error->getMessage());
}
