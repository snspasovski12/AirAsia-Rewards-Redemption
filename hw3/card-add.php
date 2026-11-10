
<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$cardName = '';
$cardType = '';
$cardValue = '';
$points = '';
$errors = [];

$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $getPostValue = static function (string $key): string {
        $value = $_POST[$key] ?? '';
        return is_string($value) ? trim($value) : '';
    };

    $cardName = $getPostValue('cardName');
    $cardType = $getPostValue('cardType');
    $cardValue = $getPostValue('cardValue');
    $points = $getPostValue('points');

    // Validate using the shared functions in validation.php
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

    // Insert only when all submitted values are valid
    if ($errors === []) {
        try {
            $statement = $pdo->prepare(
                'INSERT INTO GIFTCARD
                 (cardName, cardType, cardValue, points)
                 VALUES (:cardName, :cardType, :cardValue, :points)'
            );

            $statement->execute([
                'cardName' => $cardName,
                'cardType' => $cardType,
                'cardValue' => (float) $cardValue,
                'points' => $validatedPoints,
            ]);

            header('Location: card-list.php', true, 303);
            exit;

        } catch (PDOException $exception) {
            error_log(
                'Gift card insert failed: ' . $exception->getMessage()
            );

            $errors[] = 'The gift card could not be saved. Please try again.';
        }
    }
}

$pageTitle = 'Add Gift Card | AirAsia Rewards Redemption';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="add-form-main">
    <h2 class="add-page-heading">Add Gift Card</h2>

    <section class="gift-form-container" aria-labelledby="add-form-heading">

        <h3 id="add-form-heading" class="visually-hidden">
            Gift Card Information
        </h3>

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

        <form action="card-add.php" method="post">

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
                    Submit
                </button>

                <a class="add-form-cancel" href="card-list.php">
                    Cancel
                </a>
            </div>

        </form>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
