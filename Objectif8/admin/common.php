<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
function admin_ensure_project_schema(mysqli $conn): void {
    foreach (["admin_notes" => "TEXT NULL AFTER status", "archived_at" => "DATETIME NULL AFTER admin_notes"] as $column => $definition) {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM projects LIKE '" . $column . "'");
        if (mysqli_num_rows($check) === 0) {
            mysqli_query($conn, "ALTER TABLE projects ADD COLUMN " . $column . " " . $definition);
        }
        mysqli_free_result($check);
    }
    $result = mysqli_query($conn, "SHOW COLUMNS FROM projects LIKE 'status'");
    $statusColumn = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    $type = $statusColumn["Type"] ?? "";
    if (strpos($type, "Submitted") === false || strpos($type, "Reviewed") === false) {
        mysqli_query($conn, "ALTER TABLE projects MODIFY status ENUM('Pending','In Progress','Accepted','Rejected','Submitted','Reviewed') NOT NULL DEFAULT 'Pending'");
        mysqli_query($conn, "UPDATE projects SET status='Submitted' WHERE status='Pending'");
        mysqli_query($conn, "UPDATE projects SET status='Reviewed' WHERE status='In Progress'");
        mysqli_query($conn, "ALTER TABLE projects MODIFY status ENUM('Submitted','Reviewed','Rejected','Accepted') NOT NULL DEFAULT 'Submitted'");
    }
}
?>
