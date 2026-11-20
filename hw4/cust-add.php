<?php
require_once __DIR__ . '/includes/auth.php';
requireAdmin();

require_once __DIR__ . '/includes/db.php';

$firstName = '';
$lastName = '';
$userName = '';
$password = '';
$accountType = 'Standard Rewards';
$points = '0';
$accountTypes = ['Standard Rewards', 'Premium Rewards', 'Family Rewards'];
$errors = [];
$createdCustomer = null;
$escape = static fn ($value): string =>
    htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? null;

    if (!is_string($csrfToken) || !verifyCsrfToken($csrfToken)) {
        http_response_code(403);
        $errors[] = 'The security token is invalid. Please try again.';
    } else {
        $invalidInputShape = false;
        $getPostValue = static function (string $key, bool $trim = true) use (&$invalidInputShape): string {
            $value = $_POST[$key] ?? null;

            if (!is_string($value)) {
                $invalidInputShape = true;
                return '';
            }

            return $trim ? trim($value) : $value;
        };

        $firstName = $getPostValue('firstName');
        $lastName = $getPostValue('lastName');
        $userName = $getPostValue('userName');
        $password = $getPostValue('password', false);
        $accountType = $getPostValue('accountType');
        $points = $getPostValue('points');

        if ($invalidInputShape) {
            $errors[] = 'Please submit valid form values.';
        }

        $validateUserText = static function (string $value, string $label) use (&$errors): void {
            if ($value === '') {
                $errors[] = $label . ' is required.';
                return;
            }

            if (str_contains($value, "\0")) {
                $errors[] = $label . ' must contain valid text.';
                return;
            }

            $length = preg_match_all('/./us', $value);

            if ($length === false) {
                $errors[] = $label . ' must contain valid text.';
            } elseif ($length > 100) {
                $errors[] = $label . ' must be no more than 100 characters.';
            }
        };

        $validateUserText($firstName, 'First Name');
        $validateUserText($lastName, 'Last Name');
        $validateUserText($userName, 'Username');

        if ($password === '') {
            $errors[] = 'Password is required.';
        } elseif (str_contains($password, "\0") || preg_match('//u', $password) !== 1) {
            $errors[] = 'Password must contain valid text.';
        } elseif (PASSWORD_DEFAULT === PASSWORD_BCRYPT && strlen($password) > 72) {
            $errors[] = 'Password must be no more than 72 bytes when using the current password hashing algorithm.';
        }

        if (!in_array($accountType, $accountTypes, true)) {
            $errors[] = 'Please choose a valid account type.';
        }

        $validatedPoints = filter_var(
            $points,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );

        if ($validatedPoints === false) {
            $errors[] = 'Starting points must be a non-negative whole number.';
        }

        if ($errors === []) {
            try {
                $pdo->beginTransaction();

                $duplicateCheck = $pdo->prepare(
                    'SELECT userId FROM USER WHERE userName = :userName LIMIT 1'
                );
                $duplicateCheck->execute(['userName' => $userName]);

                if ($duplicateCheck->fetchColumn() !== false) {
                    $pdo->rollBack();
                    $errors[] = 'That username is already in use.';
                } else {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                    $userInsert = $pdo->prepare(
                        'INSERT INTO USER (userName, password, firstName, lastName, role)
                         VALUES (:userName, :password, :firstName, :lastName, :role)'
                    );
                    $userInsert->execute([
                        'userName' => $userName,
                        'password' => $passwordHash,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'role' => 'Customer',
                    ]);

                    $userId = (int) $pdo->lastInsertId();
                    $accountInsert = $pdo->prepare(
                        'INSERT INTO ACCOUNT (userId, accountType, points)
                         VALUES (:userId, :accountType, :points)'
                    );
                    $accountInsert->execute([
                        'userId' => $userId,
                        'accountType' => $accountType,
                        'points' => $validatedPoints,
                    ]);

                    $pdo->commit();
                    $createdCustomer = [
                        'userId' => $userId,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'userName' => $userName,
                        'accountType' => $accountType,
                        'points' => $validatedPoints,
                    ];
                    $password = '';
                    unset($_SESSION['csrf_token']);
                }
            } catch (PDOException $exception) {
                if ($pdo->inTransaction()) {
                    try {
                        $pdo->rollBack();
                    } catch (PDOException $rollbackException) {
                        error_log('Customer creation rollback failed: ' . $rollbackException->getMessage());
                    }
                }

                if (
                    str_contains($exception->getMessage(), 'UNIQUE constraint failed: USER.userName')
                ) {
                    $errors[] = 'That username is already in use.';
                } else {
                    error_log('Customer creation failed: ' . $exception->getMessage());
                    $errors[] = 'The customer could not be saved. Please try again.';
                }
            }
        }
    }
}

$pageTitle = 'Add Customer | AirAsia Rewards Redemption';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<main class="add-form-main">
    <h2 class="add-page-heading">Add Customer</h2>

    <section class="gift-form-container" aria-labelledby="add-customer-heading">
        <h3 id="add-customer-heading" class="visually-hidden">
            Customer Information
        </h3>

        <?php if ($createdCustomer !== null): ?>
            <div role="status">
                <p>Customer created successfully.</p>
                <dl>
                    <div>
                        <dt>Customer ID</dt>
                        <dd><?= $escape($createdCustomer['userId']) ?></dd>
                    </div>
                    <div>
                        <dt>Name</dt>
                        <dd><?= $escape($createdCustomer['firstName'] . ' ' . $createdCustomer['lastName']) ?></dd>
                    </div>
                    <div>
                        <dt>Username</dt>
                        <dd><?= $escape($createdCustomer['userName']) ?></dd>
                    </div>
                    <div>
                        <dt>Account type</dt>
                        <dd><?= $escape($createdCustomer['accountType']) ?></dd>
                    </div>
                    <div>
                        <dt>Starting points</dt>
                        <dd><?= $escape(number_format($createdCustomer['points'])) ?></dd>
                    </div>
                </dl>
            </div>
            <p><a class="back-to-list" href="card-list.php">Return to Gift Card List</a></p>
        <?php else: ?>
            <p class="required-note">
                <span aria-hidden="true">*</span> Required fields.
                New accounts default to Standard Rewards with 0 points; these are application choices, not values specified by the requirements.
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

            <form action="cust-add.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= $escape(csrfToken()) ?>">

                <div class="form-field">
                    <label for="first-name">
                        First Name
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="first-name"
                        name="firstName"
                        type="text"
                        maxlength="100"
                        autocomplete="given-name"
                        value="<?= $escape($firstName) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="last-name">
                        Last Name
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="last-name"
                        name="lastName"
                        type="text"
                        maxlength="100"
                        autocomplete="family-name"
                        value="<?= $escape($lastName) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="username">
                        Username
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="username"
                        name="userName"
                        type="text"
                        maxlength="100"
                        autocomplete="username"
                        value="<?= $escape($userName) ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="password">
                        Password
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="account-type">
                        Account Type
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <select id="account-type" name="accountType" required>
                        <?php foreach ($accountTypes as $type): ?>
                            <option value="<?= $escape($type) ?>" <?= $accountType === $type ? 'selected' : '' ?>>
                                <?= $escape($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label for="points">
                        Starting Points
                        <span class="required-indicator" aria-hidden="true">*</span>
                    </label>
                    <input
                        id="points"
                        name="points"
                        type="number"
                        min="0"
                        step="1"
                        inputmode="numeric"
                        value="<?= $escape($points) ?>"
                        required
                    >
                </div>

                <div class="add-form-actions">
                    <button class="add-form-submit" type="submit">Add Customer</button>
                    <a class="add-form-cancel" href="card-list.php">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
