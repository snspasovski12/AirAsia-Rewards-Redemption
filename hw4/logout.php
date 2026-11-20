<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: login.php', true, 303);
    exit;
}

$_SESSION = [];

$cookieParameters = session_get_cookie_params();
setcookie(
    session_name(),
    '',
    [
        'expires' => time() - 42000,
        'path' => $cookieParameters['path'],
        'domain' => $cookieParameters['domain'] ?? '',
        'secure' => $cookieParameters['secure'],
        'httponly' => $cookieParameters['httponly'],
        'samesite' => $cookieParameters['samesite'] ?? 'Lax',
    ]
);

session_destroy();

header('Location: login.php', true, 303);
exit;
