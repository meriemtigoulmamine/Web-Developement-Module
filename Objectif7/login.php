<?php
session_start();
require_once "db_connection.php";
$message = isset($_GET["registered"]) ? "Registration successful. You can now log in." : "";

if (isset($_POST["login"])) {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@esi\.dz$/i', $email)) {
        $message = "Use a valid ESI email address ending in @esi.dz.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, fullname, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            unset($_SESSION["admin_id"], $_SESSION["admin_username"]);
            $_SESSION["user_id"] = (int)$user["id"];
            $_SESSION["user_name"] = $user["fullname"];
            header("Location: index.php#home");
            exit;
        }
        $message = "Incorrect ESI email or password.";
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Researcher Login</title><link rel="stylesheet" href="styleauth.css"></head><body>
<h1>Researcher Login</h1><p><?= htmlspecialchars($message) ?></p>
<form method="post"><label for="email">ESI email:</label><input type="email" id="email" name="email" required><br><br><label for="password">Password:</label><input type="password" id="password" name="password" required><br><br><button type="submit" name="login">Log in</button></form>
<p>Not registered yet? <a href="inscription.php">Register</a></p><p><a href="admin/login.php">Administrator login</a></p>
</body></html>