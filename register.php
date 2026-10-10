<?php
session_start();
require "config.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif (strlen($password) < 8) {
        $error = "Password must have at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $check->execute([$email]);

        if ($check->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password)
                     VALUES (?, ?, ?)"
                );
                $stmt->execute([$name, $email, $hashed]);

                $success = "Account created! You can now login.";
                $name = "";
                $email = "";
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | AuthSpace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="auth-card">
    <div class="brand">AUTH<span>SPACE</span></div>

    <h1>Create Account</h1>
    <p class="subtitle">Join us and get started today.</p>

    <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="name" required
               maxlength="100"
               value="<?= htmlspecialchars($name) ?>"
               placeholder="Enter your name">

        <label>Email Address</label>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($email) ?>"
               placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password"
               minlength="8" required
               placeholder="Minimum 8 characters">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password"
               minlength="8" required
               placeholder="Confirm password">

        <button type="submit">Create Account</button>
    </form>

    <p class="switch">
        Already have an account?
        <a href="login.php">Sign In</a>
    </p>
</main>

</body>
</html>