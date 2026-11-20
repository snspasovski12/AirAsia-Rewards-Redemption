<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (!isCustomer()) {
    http_response_code(403);
    exit;
}

$userId = filter_var(
    $_SESSION['userId'],
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($userId === false) {
    http_response_code(403);
    exit;
}

require_once __DIR__ . '/includes/db.php';

$customer = null;
$accounts = [];
$databaseError = false;

try {
    $statement = $pdo->prepare(
        'SELECT
            customer.firstName,
            customer.lastName,
            customer.userName,
            account.accountId,
            account.accountType,
            account.points
         FROM USER AS customer
         LEFT JOIN ACCOUNT AS account ON account.userId = customer.userId
         WHERE customer.userId = :userId
         ORDER BY account.accountId'
    );
    $statement->execute(['userId' => $userId]);

    foreach ($statement->fetchAll() as $row) {
        if ($customer === null) {
            $customer = [
                'firstName' => $row['firstName'],
                'lastName' => $row['lastName'],
                'userName' => $row['userName'],
            ];
        }

        if ($row['accountId'] !== null) {
            $accounts[] = [
                'accountType' => $row['accountType'],
                'points' => $row['points'],
            ];
        }
    }
} catch (PDOException $exception) {
    error_log('Customer profile query failed: ' . $exception->getMessage());
    http_response_code(500);
    $databaseError = true;
}

if (!$databaseError && $customer === null) {
    http_response_code(404);
}

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageTitle = 'Customer Profile | AirAsia Rewards Redemption';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="details-main profile-main">
    <h2 class="details-page-heading">Customer Profile</h2>

    <?php if ($databaseError): ?>
        <p role="alert">Your profile could not be loaded. Please try again later.</p>
    <?php elseif ($customer === null): ?>
        <p role="alert">Customer profile not found.</p>
    <?php else: ?>
        <section class="gift-form-container" aria-labelledby="profile-information-heading">
            <section aria-labelledby="profile-information-heading">
                <h3 id="profile-information-heading">Customer Information</h3>
                <dl class="card-facts">
                    <div>
                        <dt>First Name</dt>
                        <dd><?= $escape($customer['firstName']) ?></dd>
                    </div>
                    <div>
                        <dt>Last Name</dt>
                        <dd><?= $escape($customer['lastName']) ?></dd>
                    </div>
                    <div>
                        <dt>Username</dt>
                        <dd><?= $escape($customer['userName']) ?></dd>
                    </div>
                </dl>
            </section>

            <section class="profile-accounts-section" aria-labelledby="profile-accounts-heading">
                <h3 id="profile-accounts-heading">Rewards Accounts</h3>

                <?php if ($accounts === []): ?>
                    <p>No rewards accounts are currently associated with this profile.</p>
                <?php else: ?>
                    <ul class="profile-account-list">
                        <?php foreach ($accounts as $index => $account): ?>
                            <li class="profile-account">
                                <h4>Account <?= $escape($index + 1) ?></h4>
                                <dl class="card-facts">
                                    <div>
                                        <dt>Account Type</dt>
                                        <dd><?= $escape($account['accountType']) ?></dd>
                                    </div>
                                    <div>
                                        <dt>Available Rewards Points</dt>
                                        <dd><?= $escape(number_format((int) $account['points'])) ?></dd>
                                    </div>
                                </dl>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </section>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
