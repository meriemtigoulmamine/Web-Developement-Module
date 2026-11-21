<?php
session_start();
require_once "../db_connection.php";
if (!isset($_SESSION["admin_id"])) { header("Location: login.php"); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: users.php"); exit; }
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$action = $_POST["action"] ?? "update";
if (!$id) { http_response_code(400); exit("Identifiant invalide."); }
if ($action === "delete") {
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (!mysqli_stmt_execute($stmt)) { http_response_code(500); exit("Impossible de supprimer cet utilisateur."); }
    mysqli_stmt_close($stmt);
    header("Location: users.php?deleted=1"); exit;
}
$name = trim($_POST["fullname"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
if ($action !== "update" || $name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); exit("Informations invalides."); }
$stmt = mysqli_prepare($conn, "UPDATE users SET fullname = ?, email = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt, "ssi", $name, $email, $id);
if (!mysqli_stmt_execute($stmt)) { http_response_code(400); exit("Impossible d'enregistrer: adresse email peut-être déjà utilisée."); }
mysqli_stmt_close($stmt);
header("Location: users.php?saved=1"); exit;
?>