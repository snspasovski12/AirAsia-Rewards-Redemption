<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AirAsia Rewards Redemption</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="site-header card-list-header">
        <p class="brand">AirAsia Rewards</p>
        <h1 class="redemption-title">Rewards Redemption</h1>
    </header>

    <main class="login-main">
        <section class="login-box" aria-labelledby="login-heading">
            <h2 id="login-heading">AirAsia Rewards Login</h2>
            <form action="card-list.php" method="get">
                <div class="form-field">
                    <label for="username">Username</label>
                    <input id="username" type="text" maxlength="100" autocomplete="username" required>
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" maxlength="100" autocomplete="current-password" required>
                </div>

                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
