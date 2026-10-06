<?php
/**
 * Authentication & Session Management
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    // Configure secure session cookie parameters
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_start();
}

/**
 * Check if current user is logged in as admin
 */
function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication, or redirect to login
 */
function require_admin(): void
{
    if (!is_admin_logged_in()) {
        $adminLoginUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/admin'), '/\\') . '/index.php';
        header('Location: ' . $adminLoginUrl);
        exit;
    }
}

/**
 * Log in admin user and regenerate session ID
 */
function login_admin(array $admin): void
{
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['last_login_time'] = time();
}

/**
 * Logout admin and destroy session
 */
function logout_admin(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}
