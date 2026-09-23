<?php
/**
 * NOVA E-Commerce — Application Bootstrap
 * Hardened session handling + CSRF token helpers shared by
 * admin.php (forms) and the storefront JSON endpoints.
 */

if (!function_exists('nova_session_start')) {
  /**
   * Start the PHP session with hardened cookie parameters.
   * Safe to call multiple times.
   */
  function nova_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
      return;
    }
    session_set_cookie_params([
      'lifetime' => 0,
      'path'     => '/',
      'domain'   => '',
      'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
    session_start();
  }
}

if (!function_exists('nova_csrf_token')) {
  /** Get (or lazily create) the CSRF token for the active session. */
  function nova_csrf_token(): string {
    if (empty($_SESSION['nova_csrf'])) {
      $_SESSION['nova_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['nova_csrf'];
  }
}

if (!function_exists('nova_csrf_field')) {
  /** Hidden input for HTML forms. */
  function nova_csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="'
      . htmlspecialchars(nova_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
  }
}

if (!function_exists('nova_csrf_verify')) {
  /** Validate the submitted CSRF token (constant-time compare). */
  function nova_csrf_verify(?array $payload = null): bool {
    $payload = $payload ?? $_POST;
    $token = (string) ($payload['csrf_token'] ?? '');
    return !empty($_SESSION['nova_csrf'])
      && hash_equals($_SESSION['nova_csrf'], $token);
  }
}