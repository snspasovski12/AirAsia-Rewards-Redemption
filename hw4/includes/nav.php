<?php
require_once __DIR__ . '/auth.php';

$navEscape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<nav class="main-navigation" aria-label="Main navigation">
    <div class="navigation-links">
        <a class="back-to-list" href="card-list.php">Gift Card List</a>
        <?php if (isAdmin()): ?>
            <a class="add-gift-card" href="card-add.php">Add Gift Card</a>
            <a class="add-gift-card" href="cust-add.php">Add Customer</a>
        <?php endif; ?>
        <?php if (isCustomer()): ?>
            <a class="back-to-list" href="customer-profile.php">Customer Profile</a>
        <?php endif; ?>
        <?php if (hasAnyRole(['Customer', 'Admin'])): ?>
            <a class="back-to-list" href="redeem.php">Redeem Gift Cards</a>
        <?php endif; ?>
    </div>

    <?php if (isAuthenticated()): ?>
        <div class="session-controls">
            <span class="session-user">
                <?= $navEscape($_SESSION['userName']) ?>
                (<?= $navEscape($_SESSION['role']) ?>)
            </span>
            <form action="logout.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= $navEscape(csrfToken()) ?>">
                <button class="logout-button" type="submit">Logout</button>
            </form>
        </div>
    <?php endif; ?>
</nav>