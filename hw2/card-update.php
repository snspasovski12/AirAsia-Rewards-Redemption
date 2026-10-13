<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Card | AirAsia Rewards Redemption</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="site-header card-list-header">
        <p class="brand">AirAsia Rewards</p>
        <h1 class="redemption-title">Rewards Redemption</h1>
    </header>

    <main class="add-form-main">
        <h2 class="add-page-heading">Update Gift Card</h2>
        <section class="gift-form-container" aria-labelledby="update-form-heading">
            <h3 id="update-form-heading" class="visually-hidden">Gift Card Information</h3>
            <p class="required-note"><span aria-hidden="true">*</span> Required fields</p>
            <form action="card-list.php" method="get">
                <div class="form-field">
                    <label for="card-name">Card Name <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="card-name" type="text" maxlength="100" value="AirAsia Travel eGift Card - Silver" required>
                </div>

                <div class="form-field">
                    <label for="card-type">Card Type <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="card-type" type="text" maxlength="100" value="Travel eGift Card" required>
                </div>

                <div class="form-field">
                    <label for="card-value">Card Value <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="card-value" type="number" min="0" step="0.01" inputmode="decimal" value="50.00" required>
                </div>

                <div class="form-field">
                    <label for="points-required">Points Required <span class="required-indicator" aria-hidden="true">*</span></label>
                    <input id="points-required" type="number" min="0" step="1" inputmode="numeric" value="5000" required>
                </div>

                <div class="add-form-actions">
                    <button class="add-form-submit" type="submit">Update</button>
                    <a class="add-form-cancel" href="card-list.php">Cancel</a>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
