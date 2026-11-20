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

$responseStatus = 200;
$errorMessage = null;
$redemption = null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    $responseStatus = 405;
    $errorMessage = 'Redemptions must be submitted using the confirmation form.';
} else {
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/validation.php';

    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!is_string($csrfToken) || !verifyCsrfToken($csrfToken)) {
        $responseStatus = 403;
        $errorMessage = 'The security token is invalid. Reload the redemption form and try again.';
    } else {
        $cardId = validateCardId($_POST['cardId'] ?? null);
        $accountId = validateCardId($_POST['accountId'] ?? null);
        $confirmed = ($_POST['confirmRedemption'] ?? null) === 'yes';

        if ($cardId === false) {
            $responseStatus = 400;
            $errorMessage = 'Please select a valid gift card.';
        } elseif ($accountId === false) {
            $responseStatus = 400;
            $errorMessage = 'Please select a valid rewards account.';
        } elseif (!$confirmed) {
            $responseStatus = 400;
            $errorMessage = 'Confirm the redemption before submitting it.';
        } else {
            try {
                $pdo->exec('BEGIN IMMEDIATE');

                $cardStatement = $pdo->prepare(
                    'SELECT cardId, cardName, cardValue, points
                     FROM GIFTCARD
                     WHERE cardId = :cardId'
                );
                $cardStatement->execute(['cardId' => $cardId]);
                $card = $cardStatement->fetch();

                if ($card === false) {
                    $pdo->rollBack();
                    $responseStatus = 404;
                    $errorMessage = 'The selected gift card could not be found.';
                } else {
                    $accountStatement = $pdo->prepare(
                        'SELECT accountId, accountType, points
                         FROM ACCOUNT
                         WHERE accountId = :accountId
                           AND userId = :userId'
                    );
                    $accountStatement->execute([
                        'accountId' => $accountId,
                        'userId' => $userId,
                    ]);
                    $account = $accountStatement->fetch();

                    if ($account === false) {
                        $pdo->rollBack();
                        $responseStatus = 404;
                        $errorMessage = 'The selected rewards account is not available for your profile.';
                    } else {
                        $pointsRequired = (int) $card['points'];
                        $currentPoints = (int) $account['points'];

                        if ($currentPoints < $pointsRequired) {
                            $pdo->rollBack();
                            $responseStatus = 409;
                            $errorMessage = 'This account does not have enough points for that gift card.';
                        } else {
                            $deductPoints = $pdo->prepare(
                                'UPDATE ACCOUNT
                                 SET points = points - :deductPoints
                                 WHERE accountId = :accountId
                                   AND userId = :userId
                                   AND points >= :requiredPoints'
                            );
                            $deductPoints->execute([
                                'deductPoints' => $pointsRequired,
                                'accountId' => $accountId,
                                'userId' => $userId,
                                'requiredPoints' => $pointsRequired,
                            ]);

                            if ($deductPoints->rowCount() !== 1) {
                                $pdo->rollBack();
                                $responseStatus = 409;
                                $errorMessage = 'This account does not have enough points for that gift card.';
                            } else {
                                $redemptionInsert = $pdo->prepare(
                                    'INSERT INTO REDEMPTION
                                        (date, accountId, cardId, pointsRedeemed)
                                     VALUES
                                        (:date, :accountId, :cardId, :pointsRedeemed)'
                                );
                                $redemptionInsert->execute([
                                    'date' => date('Y-m-d'),
                                    'accountId' => $accountId,
                                    'cardId' => $cardId,
                                    'pointsRedeemed' => $pointsRequired,
                                ]);

                                $pdo->commit();

                                $redemption = [
                                    'cardName' => $card['cardName'],
                                    'pointsRedeemed' => $pointsRequired,
                                    'updatedBalance' => $currentPoints - $pointsRequired,
                                ];
                            }
                        }
                    }
                }
            } catch (PDOException $exception) {
                if ($pdo->inTransaction()) {
                    try {
                        $pdo->rollBack();
                    } catch (PDOException $rollbackException) {
                        error_log('Gift card redemption rollback failed: ' . $rollbackException->getMessage());
                    }
                }

                error_log('Gift card redemption failed: ' . $exception->getMessage());
                $responseStatus = 500;
                $errorMessage = 'The redemption could not be completed. Please try again later.';
            }
        }
    }
}

http_response_code($responseStatus);
$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$pageTitle = $redemption === null
    ? 'Redemption Error | AirAsia Rewards Redemption'
    : 'Redemption Successful | AirAsia Rewards Redemption';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="details-main redemption-main">
    <?php if ($redemption !== null): ?>
        <h2 class="details-page-heading">Redemption Successful</h2>
        <section class="gift-form-container" role="status">
            <p>Your gift card has been redeemed.</p>
            <dl class="card-facts">
                <div>
                    <dt>Gift Card</dt>
                    <dd><?= $escape($redemption['cardName']) ?></dd>
                </div>
                <div>
                    <dt>Points Redeemed</dt>
                    <dd><?= $escape(number_format($redemption['pointsRedeemed'])) ?></dd>
                </div>
                <div>
                    <dt>Updated Available Balance</dt>
                    <dd><?= $escape(number_format($redemption['updatedBalance'])) ?></dd>
                </div>
            </dl>
            <p><a class="back-to-list" href="redeem.php">Redeem another gift card</a></p>
        </section>
    <?php else: ?>
        <h2 class="details-page-heading">Redemption Not Completed</h2>
        <p role="alert"><?= $escape($errorMessage) ?></p>
        <p><a class="back-to-list" href="redeem.php">Return to gift cards</a></p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
