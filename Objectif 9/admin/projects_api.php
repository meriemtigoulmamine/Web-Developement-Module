<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

function respond($ok, $message = "", $projects = null, $statusCode = 200) {
    http_response_code($statusCode);
    $body = ["success" => $ok, "message" => $message];
    if ($projects !== null) $body["projects"] = $projects;
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (!isset($_SESSION["admin_id"])) {
    respond(false, "Session administrateur expirée. Reconnectez-vous.", null, 401);
}

require_once "../db_connection.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $archived = isset($_GET["archived"]) && $_GET["archived"] === "1" ? 1 : 0;
    $sql = "SELECT p.id,p.title,p.description,p.program,p.calls,p.status,p.submitted_at,p.remarks,u.fullname,u.email
            FROM projects p INNER JOIN users u ON p.user_id=u.id
            WHERE p.archived=? ORDER BY p.submitted_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) respond(false, "Impossible de charger les soumissions.", null, 500);
    mysqli_stmt_bind_param($stmt, "i", $archived);
    if (!mysqli_stmt_execute($stmt)) respond(false, "Impossible de charger les soumissions.", null, 500);
    $result = mysqli_stmt_get_result($stmt);
    $projects = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row["id"] = (int)$row["id"];
        $projects[] = $row;
    }
    mysqli_stmt_close($stmt);
    respond(true, "", $projects);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(false, "Méthode non autorisée.", null, 405);
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$action = $_POST["action"] ?? "";
if (!$id) respond(false, "Identifiant de projet invalide.", null, 400);

if ($action === "save") {
    $status = $_POST["status"] ?? "";
    $allowed = ["Submitted", "Reviewed", "Rejected", "Accepted"];
    $remarks = trim($_POST["remarks"] ?? "");
    if (!in_array($status, $allowed, true)) respond(false, "Statut invalide.", null, 400);
    $stmt = mysqli_prepare($conn, "UPDATE projects SET status=?,remarks=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $remarks, $id);
} elseif ($action === "archive" || $action === "unarchive") {
    $archived = $action === "archive" ? 1 : 0;
    $stmt = mysqli_prepare($conn, "UPDATE projects SET archived=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ii", $archived, $id);
} elseif ($action === "delete") {
    $stmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
} else {
    respond(false, "Action invalide.", null, 400);
}

if (!$stmt || !mysqli_stmt_execute($stmt)) {
    respond(false, "La modification n'a pas pu être enregistrée.", null, 500);
}
mysqli_stmt_close($stmt);
respond(true, "Modification enregistrée.");
?>