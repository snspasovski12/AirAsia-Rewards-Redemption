
<?php
require_once __DIR__ . '/includes/validation.php';

$pageTitle = 'Update Gift Card | AirAsia Rewards Redemption';

// Validate the selected gift card ID using the shared function.
$cardId = validateCardId($_GET['card_id'] ?? null);

$card = null;
$pageError = null;
$errors = [];
$cardName = '';
$cardType = '';
$cardValue = '';
$points = '';

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

if ($cardId === false) {
    http_response_code(400);
    $pageError = 'Invalid gift card ID.';
} else {
    require_once __DIR__ . '/includes/db.php';

    try {
        // Retrieve the existing gift card.
        $statement = $pdo->prepare(
            'SELECT cardId, cardName, cardType, cardValue, points
             FROM GIFTCARD
             WHERE cardId = :card_id'
        );

        $statement->execute(['card_id' => $cardId]);
        $card = $statement->fetch();

        if ($card === false) {
            http_response_code(404);
            $pageError = 'Gift card not found.';
        } else {
            // Prepopulate the form with existing database values.
            $cardName = (string) $card['cardName'];
            $cardType = (string) $card['cardType'];
            $cardValue = (string) $card['cardValue'];
            $points = (string) $card['points'];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $getPostValue = static function (string $key): string {
                    $value = $_POST[$key] ?? '';
                    return is_string($value) ? trim($value) : '';
                };

                $cardName = $getPostValue('cardName');
                $cardType = $getPostValue('cardType');
                $cardValue = $getPostValue('cardValue');
                $points = $getPostValue('points');

                // Use reusable server-side validation functions.
                foreach ([
                    validateCardName($cardName),
                    validateCardType($cardType),
                    validateCardValue($cardValue),
                ] as $error) {
                    if ($error !== null) {
                        $errors[] = $error;
                    }
                }

                $pointsValidation = validatePointsRequired($points);
                $validatedPoints = $pointsValidation['value'];

                if ($pointsValidation['error'] !== null) {
                    $errors[] = $pointsValidation['error'];
                }

                // Update only when every submitted value is valid.
                if ($errors === []) {
                    try {
                        $update = $pdo->prepare(
                            'UPDATE GIFTCARD
                             SET cardName = :cardName,
                                 cardType = :cardType,
                                 cardValue = :cardValue,
                                 points = :points
                             WHERE cardId = :card_id'
                        );

                        $update->execute([
                            'cardName' => $cardName,
                            'cardType' => $cardType,
                            'cardValue' => (float) $cardValue,
                            'points' => $validatedPoints,
                            'card_id' => $cardId,
                        ]);

                        header(
                            'Location: card-details.php?card_id=' . $cardId,
                            true,
                            303
                        );
                        exit;

                    } catch (PDOException $exception) {
                        error_log(
                            'Gift card update failed: ' . $exception->getMessage()
                        );

                        $errors[] = 'The gift card could not be updated. Please try again.';
                    }
                }
            }
        }
    } catch (PDOException $exception) {
        error_log('Gift card lookup failed: ' . $exception->getMessage());
        http_response_code(500);
        $pageError = 'Unable to load the gift card. Please try again later.';
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="add-form-main">
    <h2 class="add-page-heading">Update Gift Card</h2>

    <section class="gift-form-container" aria-labelledby="update-form-heading">

        <h3 id="update-form-heading" class="visually-hidden">
            Gift Card Information
        </h3>

        <?php if ($pageError !== null): ?>
            <p role="alert"><?= $escape($pageError) ?></p>

        <?php elseif (is_array($card)): ?>
            <p class="required-note">
                <span aria-hidden="true">*</span> Required fields
            </p>

            <?php if ($errors !== []): ?>
                <div role="alert">
                    <p>Please correct the following:</p>

                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form
                action="<?= $escape('card-update.php?card_id=' . $cardId) ?>"
                method="post"
            >
                <div class="form-field">
                    <label for="card-name">
                        Card Name
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>

                    <input
                        id="card-name"
                        name="cardName"
                        type="text"
                        maxlength="100"
                        value="<?= $escape($cardName) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="card-type">
                        Card Type
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>

                    <input
                        id="card-type"
                        name="cardType"
                        type="text"
                        maxlength="100"
                        value="<?= $escape($cardType) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="card-value">
                        Card Value
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>

                    <input
                        id="card-value"
                        name="cardValue"
                        type="number"
                        min="0.01"
                        step="0.01"
                        inputmode="decimal"
                        value="<?= $escape($cardValue) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="points">
                        Points Required
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>

                    <input
                        id="points"
                        name="points"
                        type="number"
                        min="1"
                        step="1"
                        inputmode="numeric"
                        value="<?= $escape($points) ?>"
                        required
                    >
                </div>

                <div class="add-form-actions">
                    <button class="add-form-submit" type="submit">
                        Update
                    </button>

                    <a
                        class="add-form-cancel"
                        href="<?= $escape('card-details.php?card_id=' . $cardId) ?>"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
