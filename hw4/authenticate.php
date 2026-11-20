<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
    header('Location: login.php?error=1', true, 303);
    exit;
}

$username = isset($_POST['username']) ? trim((string) $_POST['username']) : '';
$password = isset($_POST['password']) ? (string) $_POST['password'] : '';

if ($username === '' || $password === '') {
    header('Location: login.php?error=1', true, 303);
    exit;
}

require_once __DIR__ . '/includes/db.php';

try {
    $statement = $pdo->prepare(
        'SELECT userId, userName, password, role
         FROM USER
         WHERE userName = :userName
         LIMIT 1'
    );
    $statement->execute(['userName' => $username]);
    $user = $statement->fetch();
} catch (PDOException $exception) {
    error_log('Login database query failed.');
    header('Location: login.php?error=1', true, 303);
    exit;
}

$passwordMatches = is_array($user)
    && password_verify($password, (string) $user['password']);

if (!$passwordMatches) {
    header('Location: login.php?error=1', true, 303);
    exit;
}

session_regenerate_id(true);

$_SESSION['userId'] = (int) $user['userId'];
$_SESSION['userName'] = (string) $user['userName'];
$_SESSION['role'] = (string) $user['role'];

header('Location: card-list.php', true, 303);
exit;
