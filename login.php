<?php
session_start();
require "config.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $error = "Please fill all fields.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, name, password FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | AuthSpace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="auth-card">
    <div class="brand">AUTH<span>SPACE</span></div>

    <h1>Welcome Back</h1>
    <p class="subtitle">Sign in to continue to your account.</p>

    <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email"
               value="<?= htmlspecialchars($email) ?>"
               placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password"
               placeholder="Enter your password" required>

        <button type="submit">Sign In</button>
    </form>

    <p class="switch">
        Don't have an account?
        <a href="register.php">Create Account</a>
    </p>
</main>

</body>
</html>
