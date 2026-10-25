<?php
session_start();
require_once "../db_connection.php";
$message = "";
if (isset($_POST["login"])) {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admins WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);
        unset($_SESSION["user_id"], $_SESSION["user_name"]);
        $_SESSION["admin_id"] = (int)$admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        header("Location: dashboard.php");
        exit;
    }
    $message = "Incorrect username or password.";
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Administrator Login</title><link rel="stylesheet" href="../styleauth.css"></head><body>
<h1>Administrator Login</h1><?php if (isset($_GET["created"])) $message = "Administrator created. Please log in."; ?><p><?= htmlspecialchars($message) ?></p>
<form method="post"><label for="username">Username:</label><input type="text" id="username" name="username" required autocomplete="username"><br><br><label for="password">Password:</label><input type="password" id="password" name="password" required autocomplete="current-password"><br><br><button type="submit" name="login">Log in</button></form>
<p><a href="../login.php">Researcher login</a></p>
</body></html>