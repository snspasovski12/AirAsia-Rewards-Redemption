
<?php
require_once __DIR__ . '/includes/db.php';

$statement = $pdo->query(
    'SELECT cardId, cardName, points FROM GIFTCARD ORDER BY cardId'
);
$giftCards = $statement->fetchAll();

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$pageTitle = 'Gift Card List | AirAsia Rewards Redemption';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="card-list-main">
    <div class="list-heading-row">
        <h2 class="list-page-heading">Gift Card List</h2>
        <a class="add-gift-card" href="card-add.php">Add Gift Card</a>
    </div>

    <?php if ($giftCards !== []): ?>
        <ul class="gift-card-list">
            <?php foreach ($giftCards as $card): ?>
                <?php
                $cardName = (string) $card['cardName'];

                $imageFile = 'airasia-silver.svg';
                if (str_ends_with($cardName, ' - Gold')) {
                    $imageFile = 'airasia-gold.svg';
                } elseif (str_ends_with($cardName, ' - Platinum')) {
                    $imageFile = 'airasia-platinum.svg';
                }

                $detailsUrl = 'card-details.php?card_id=' . (int) $card['cardId'];
                ?>
                <li>
                    <article class="gift-card">
                        <a class="gift-card-image-link" href="<?= $escape($detailsUrl) ?>">
                            <img
                                src="<?= $escape('images/' . $imageFile) ?>"
                                alt="<?= $escape($cardName) ?>"
                                loading="lazy"
                            >
                        </a>
                        <div class="gift-card-info">
                            <h3>
                                <a href="<?= $escape($detailsUrl) ?>">
                                    <?= $escape($cardName) ?>
                                </a>
                            </h3>
                            <p class="gift-card-points">
                                <span>Points required</span>
                                <strong><?= $escape(number_format((int) $card['points'])) ?> points</strong>
                            </p>
                        </div>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No gift cards are available.</p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
