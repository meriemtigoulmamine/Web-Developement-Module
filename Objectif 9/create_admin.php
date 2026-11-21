<?php
session_start();
require_once "db_connection.php";
$count_result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM admins");
$admin_exists = (int)mysqli_fetch_assoc($count_result)["total"] > 0;
$message = "";

if (!$admin_exists && isset($_POST["create_admin"])) {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    if ($username === "" || strlen($password) < 8) {
        $message = "Choose a username and a password with at least 8 characters.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $hash);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: admin/login.php?created=1");
            exit;
        }
        $message = "Could not create the administrator. The username may already exist.";
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create administrator</title><link rel="stylesheet" href="styleauth.css"></head><body>
<h1>Create the first administrator</h1>
<?php if ($admin_exists): ?><p>An administrator account already exists. This setup page is disabled.</p><?php else: ?>
<p><?= htmlspecialchars($message) ?></p><form method="post"><label for="username">Username:</label><input id="username" name="username" required><br><br><label for="password">Password (8 characters minimum):</label><input id="password" name="password" type="password" minlength="8" required><br><br><label for="confirm_password">Confirm password:</label><input id="confirm_password" name="confirm_password" type="password" minlength="8" required><br><br><button type="submit" name="create_admin">Create administrator</button></form>
<?php endif; ?><p><a href="index.php">Home</a></p>
</body></html>