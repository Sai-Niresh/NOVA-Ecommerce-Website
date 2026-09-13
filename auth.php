<?php
require_once __DIR__ . '/config/database.php';

session_start();
header('Content-Type: application/json; charset=utf-8');

function authResponse(bool $success, string $message, array $extra = []): never {
  http_response_code($success ? 200 : 400);
  echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
  exit;
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

    $query = $db->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $query->execute([$email]);
    $user = $query->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
      authResponse(false, 'Incorrect email or password.');
    }

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
    if ($newPassword !== $newPasswordConfirm) {
      authResponse(false, 'New passwords do not match.');
    }

    $update = $db->prepare('UPDATE users SET password_hash = ? WHERE email = ?');
    $update->execute([password_hash($newPassword, PASSWORD_DEFAULT), $email]);
    if ($update->rowCount() === 0) {
      authResponse(false, 'No account was found with that email address.');
    }
    authResponse(true, 'Password reset successfully. You can now sign in.');
  }

  if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    authResponse(true, 'Signed out successfully.');
  }

  authResponse(false, 'Unknown authentication action.');
} catch (Throwable $error) {
  error_log('NOVA auth error: ' . $error->getMessage());
  authResponse(false, 'Database connection failed. Start MySQL in XAMPP and try again.');
}
