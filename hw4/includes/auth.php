<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_name('AIRASIA_SESSION');
    session_start();
}

function isAuthenticated(): bool
{
    return isset($_SESSION['userId'], $_SESSION['userName'], $_SESSION['role']);
}

function requireLogin(string $redirectTo = 'login.php'): void
{
    if (isAuthenticated()) {
        return;
    }

    header('Location: ' . $redirectTo, true, 303);
    exit;
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function hasRole(string $role): bool
{
    return isAuthenticated() && $_SESSION['role'] === $role;
}

function hasAnyRole(array $roles): bool
{
    if (!isAuthenticated()) {
        return false;
    }

    return in_array($_SESSION['role'], $roles, true);
}

function isAdmin(): bool
{
    return hasRole('Admin');
}

function isCustomer(): bool
{
    return hasRole('Customer');
}

function requireAdmin(): void
{
    requireLogin();

    if (!isAdmin()) {
        http_response_code(403);
        exit;
    }
}
