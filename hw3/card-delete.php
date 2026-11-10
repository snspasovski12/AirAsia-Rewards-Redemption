
<?php
$pageTitle = 'Delete Gift Card | AirAsia Rewards Redemption';

$rawCardId = $_GET['card_id'] ?? null;
$cardId = is_string($rawCardId)
    ? filter_var($rawCardId, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ])
    : false;

$card = null;
$pageError = null;
$confirmationError = null;

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Allow: GET, POST');
    $pageError = 'This request method is not supported.';
} elseif ($cardId === false) {
    http_response_code(400);
    $pageError = 'Invalid gift card ID.';
} else {
    require_once __DIR__ . '/includes/db.php';

    try {
        $statement = $pdo->prepare(
            'SELECT cardId, cardName
             FROM GIFTCARD
             WHERE cardId = :card_id'
        );

        $statement->execute(['card_id' => $cardId]);
        $card = $statement->fetch();

        if ($card === false) {
            http_response_code(404);
            $pageError = 'Gift card not found.';
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $confirmation = $_POST['confirm_delete'] ?? null;

            if (!is_string($confirmation) || $confirmation !== '1') {
                $confirmationError = 'Use the Confirm Delete button to proceed.';
            } else {
                $delete = $pdo->prepare(
                    'DELETE FROM GIFTCARD WHERE cardId = :card_id'
                );

                $delete->execute(['card_id' => $cardId]);

                if ($delete->rowCount() === 1) {
                    header('Location: card-list.php', true, 303);
                    exit;
                }

                http_response_code(404);
                $pageError = 'Gift card not found.';
                $card = false;
            }
        }
    } catch (PDOException $exception) {
        error_log('Gift card deletion failed: ' . $exception->getMessage());
        http_response_code(500);
        $pageError = 'Unable to process the gift card. Please try again later.';
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="add-form-main">
    <h2 class="add-page-heading">Delete Gift Card</h2>

    <section class="gift-form-container" aria-labelledby="delete-card-heading">
        <h3 id="delete-card-heading" class="visually-hidden">
            Confirm Gift Card Deletion
        </h3>

        <?php if ($pageError !== null): ?>
            <p role="alert"><?= $escape($pageError) ?></p>

        <?php elseif (is_array($card)): ?>
            <?php if ($confirmationError !== null): ?>
                <p role="alert"><?= $escape($confirmationError) ?></p>
            <?php endif; ?>

            <p>
                Are you sure you want to delete
                <strong><?= $escape($card['cardName']) ?></strong>?
            </p>

            <div class="add-form-actions">
                <form
                    action="<?= $escape('card-delete.php?card_id=' . (int) $card['cardId']) ?>"
                    method="post"
                >
                    <input
                        type="hidden"
                        name="confirm_delete"
                        value="1"
                    >

                    <button class="add-form-submit" type="submit">
                        Confirm Delete
                    </button>
                </form>

                <a
                    class="add-form-cancel"
                    href="<?= $escape('card-details.php?card_id=' . (int) $card['cardId']) ?>"
                >
                    Cancel
                </a>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
