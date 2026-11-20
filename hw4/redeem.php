<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

if (!hasAnyRole(['Customer', 'Admin'])) {
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
require_once __DIR__ . '/includes/validation.php';

$cards = [];
$selectedCard = null;
$accounts = [];
$errorMessage = null;

if (!array_key_exists('card_id', $_GET)) {
    try {
        $cards = $pdo->query(
            'SELECT cardId, cardName, cardValue, points
             FROM GIFTCARD
             ORDER BY cardId'
        )->fetchAll();
    } catch (PDOException $exception) {
        error_log('Gift card redemption catalog query failed: ' . $exception->getMessage());
        http_response_code(500);
        $errorMessage = 'Available gift cards could not be loaded. Please try again later.';
    }
} else {
    $cardId = validateCardId($_GET['card_id']);

    if ($cardId === false) {
        http_response_code(400);
        $errorMessage = 'Please select a valid gift card.';
    } else {
        try {
            $cardStatement = $pdo->prepare(
                'SELECT cardId, cardName, cardValue, points
                 FROM GIFTCARD
                 WHERE cardId = :cardId'
            );
            $cardStatement->execute(['cardId' => $cardId]);
            $selectedCard = $cardStatement->fetch();

            if ($selectedCard === false) {
                http_response_code(404);
                $errorMessage = 'The selected gift card could not be found.';
            } else {
                $accountStatement = $pdo->prepare(
                    'SELECT accountId, accountType, points
                     FROM ACCOUNT
                     WHERE userId = :userId
                     ORDER BY accountId'
                );
                $accountStatement->execute(['userId' => $userId]);
                $accounts = $accountStatement->fetchAll();
            }
        } catch (PDOException $exception) {
            error_log('Gift card redemption selection query failed: ' . $exception->getMessage());
            http_response_code(500);
            $errorMessage = 'Redemption details could not be loaded. Please try again later.';
        }
    }
}

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageTitle = 'Redeem Gift Cards | AirAsia Rewards Redemption';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="details-main redemption-main">
    <h2 class="details-page-heading">Redeem Gift Cards</h2>

    <?php if ($errorMessage !== null): ?>
        <p role="alert"><?= $escape($errorMessage) ?></p>
        <p><a class="back-to-list" href="redeem.php">Return to gift cards</a></p>
    <?php elseif ($selectedCard === null): ?>
        <p>Select a gift card to review the redemption details.</p>

        <?php if ($cards === []): ?>
            <p>No gift cards are currently available.</p>
        <?php else: ?>
            <ul class="gift-card-list">
                <?php foreach ($cards as $card): ?>
                    <li>
                        <article class="gift-card">
                            <div class="gift-card-info">
                                <div>
                                    <h3><?= $escape($card['cardName']) ?></h3>
                                    <p>Value: $<?= $escape(number_format((float) $card['cardValue'], 2)) ?></p>
                                </div>
                                <p class="gift-card-points">
                                    <span>Points required</span>
                                    <strong><?= $escape(number_format((int) $card['points'])) ?></strong>
                                </p>
                                <a
                                    class="add-gift-card"
                                    href="<?= $escape('redeem.php?card_id=' . (int) $card['cardId']) ?>"
                                >
                                    Select Gift Card
                                </a>
                            </div>
                        </article>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php else: ?>
        <section class="gift-form-container" aria-labelledby="selected-card-heading">
            <h3 id="selected-card-heading"><?= $escape($selectedCard['cardName']) ?></h3>
            <dl class="card-facts">
                <div>
                    <dt>Gift Card Value</dt>
                    <dd>$<?= $escape(number_format((float) $selectedCard['cardValue'], 2)) ?></dd>
                </div>
                <div>
                    <dt>Points Required</dt>
                    <dd><?= $escape(number_format((int) $selectedCard['points'])) ?></dd>
                </div>
            </dl>

            <?php if ($accounts === []): ?>
                <p role="alert">No rewards account is available for your profile.</p>
            <?php else: ?>
                <p>Choose the account to use. Your balance will be checked again when you confirm.</p>

                <form action="process-redemption.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $escape(csrfToken()) ?>">
                    <input type="hidden" name="cardId" value="<?= $escape($selectedCard['cardId']) ?>">

                    <div class="form-field">
                        <label for="account-id">Rewards Account</label>
                        <select id="account-id" name="accountId" required>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= $escape($account['accountId']) ?>">
                                    Account #<?= $escape($account['accountId']) ?>
                                    — <?= $escape($account['accountType']) ?>
                                    — <?= $escape(number_format((int) $account['points'])) ?> points available
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-field redemption-confirmation">
                        <label>
                            <input type="checkbox" name="confirmRedemption" value="yes" required>
                            I confirm this gift card redemption.
                        </label>
                    </div>

                    <div class="add-form-actions">
                        <button class="add-form-submit" type="submit">Confirm Redemption</button>
                        <a class="add-form-cancel" href="redeem.php">Choose Another Gift Card</a>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
