<?php
session_start();
require_once "db_connection.php";
$message = "";

if (isset($_POST["register"])) {
    $fullname = trim($_POST["fullname"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($fullname === "" || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@esi\.dz$/i', $email)) {
        $message = "Enter your name and a valid email address ending in @esi.dz.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $message = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_stmt_close($check);
                header("Location: login.php?registered=1");
                exit;
            }
            $message = "Registration failed.";
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>ESI Registration - International Mobility</title><link rel="stylesheet" href="styleauth.css"></head><body>
<h1>Create a researcher account</h1><p><?= htmlspecialchars($message) ?></p>
<form method="post"><label for="fullname">Full name:</label><input type="text" id="fullname" name="fullname" maxlength="150" required><br><br><label for="email">ESI email:</label><input type="email" id="email" name="email" required><br><br><label for="password">Password (8 characters minimum):</label><input type="password" id="password" name="password" minlength="8" required><br><br><label for="confirm_password">Confirm password:</label><input type="password" id="confirm_password" name="confirm_password" minlength="8" required><br><br><button type="submit" name="register">Register</button></form>
<p>Already have an account? <a href="login.php">Log in</a></p>
</body></html>