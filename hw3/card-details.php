
<?php
$pageTitle = 'Gift Card Details | AirAsia Rewards Redemption';

$rawCardId = $_GET['card_id'] ?? null;
$cardId = is_string($rawCardId)
    ? filter_var($rawCardId, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ])
    : false;

$card = null;
$errorMessage = null;

if ($cardId === false) {
    http_response_code(400);
    $errorMessage = 'Invalid gift card ID.';
} else {
    require_once __DIR__ . '/includes/db.php';

    $statement = $pdo->prepare(
        'SELECT cardId, cardName, cardType, cardValue, points
         FROM GIFTCARD
         WHERE cardId = :card_id'
    );
    $statement->execute(['card_id' => $cardId]);
    $card = $statement->fetch();

    if ($card === false) {
        http_response_code(404);
        $errorMessage = 'Gift card not found.';
    }
}

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="details-main">
    <div class="details-heading-row">
        <h2 class="details-page-heading">Gift Card Details</h2>
        <a class="back-to-list" href="card-list.php">Back to Gift Card List</a>
    </div>

    <?php if ($errorMessage !== null): ?>
        <p role="alert"><?= $escape($errorMessage) ?></p>
    <?php else: ?>
        <?php
        $cardName = (string) $card['cardName'];

        $imageFile = 'airasia-silver.svg';
        if (str_ends_with($cardName, ' - Gold')) {
            $imageFile = 'airasia-gold.svg';
        } elseif (str_ends_with($cardName, ' - Platinum')) {
            $imageFile = 'airasia-platinum.svg';
        }

        $cardId = (int) $card['cardId'];
        $cardActionQuery = '?card_id=' . $cardId;
        ?>
        <article class="details-card" aria-labelledby="details-card-name">
            <img
                class="details-card-image"
                src="<?= $escape('images/' . $imageFile) ?>"
                alt="<?= $escape($cardName) ?>"
            >
            <section class="details-information" aria-label="Gift card information">
                <h3 id="details-card-name"><?= $escape($cardName) ?></h3>
                <dl class="card-facts">
                    <div>
                        <dt>Card Type</dt>
                        <dd><?= $escape($card['cardType']) ?></dd>
                    </div>
                    <div>
                        <dt>Card Value</dt>
                        <dd>$<?= $escape(number_format((float) $card['cardValue'], 2)) ?></dd>
                    </div>
                    <div>
                        <dt>Points Required</dt>
                        <dd><?= $escape(number_format((int) $card['points'])) ?></dd>
                    </div>
                </dl>
                <a class="update-gift-card" href="<?= $escape('card-update.php' . $cardActionQuery) ?>">
                    Update Gift Card
                </a>
                <a class="update-gift-card" href="<?= $escape('card-delete.php' . $cardActionQuery) ?>">
                    Delete Gift Card
                </a>
            </section>
        </article>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
