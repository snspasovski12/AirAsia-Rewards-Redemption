<?php
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Login | AirAsia Rewards Redemption';
$hasLoginError = isset($_GET['error']) && $_GET['error'] === '1';
$csrfToken = csrfToken();

require_once __DIR__ . '/includes/header.php';
?>

<main class="login-main">
    <h1 class="page-title">AirAsia Rewards Login</h1>

    <div class="login-box">
        <h2>Sign in</h2>

        <?php if ($hasLoginError): ?>
            <p role="alert">Invalid username or password.</p>
        <?php endif; ?>

        <form action="authenticate.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

            <div class="form-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    maxlength="100"
                    autocomplete="username"
                >
            </div>

            <div class="form-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit">Login</button>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
