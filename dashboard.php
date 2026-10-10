<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$name = htmlspecialchars(
    $_SESSION["user_name"],
    ENT_QUOTES,
    "UTF-8"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | AuthSpace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="auth-card dashboard">
    <div class="brand">AUTH<span>SPACE</span></div>

    <div class="welcome-icon">✓</div>

    <h1>Welcome, <?= $name ?>!</h1>

    <p class="subtitle">
        You have successfully logged in to your account.
    </p>

    <div class="alert success">
        Your session is active.
    </div>

    <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>
</main>

</body>
</html>