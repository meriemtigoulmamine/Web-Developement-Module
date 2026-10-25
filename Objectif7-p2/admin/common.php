<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
function admin_csrf_valid(): bool {
    return isset($_POST["csrf_token"], $_SESSION["csrf_token"])
        && hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"]);
}
function admin_h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
