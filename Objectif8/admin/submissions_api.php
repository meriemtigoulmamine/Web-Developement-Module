<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, max-age=0");
function api_reply(int $code, array $payload): void {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}
if (!isset($_SESSION["admin_id"])) {
    api_reply(401, ["ok" => false, "message" => "Administrator sign-in is required."]);
}
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
require_once "common.php";
require_once "../db_connection.php";
try {
    admin_ensure_project_schema($conn);
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $archived = isset($_GET["archived"]) && $_GET["archived"] === "1";
        $predicate = $archived ? "projects.archived_at IS NOT NULL" : "projects.archived_at IS NULL";
        $sql = "SELECT projects.id, projects.title, projects.description, projects.duration, projects.budget,
                    projects.program, projects.calls, projects.institution, projects.country, projects.contact,
                    projects.needs, projects.other_details, projects.status, projects.admin_notes,
                    projects.submitted_at, users.fullname, users.email
                FROM projects INNER JOIN users ON projects.user_id=users.id
                WHERE $predicate ORDER BY projects.submitted_at DESC, projects.id DESC";
        $result = mysqli_query($conn, $sql);
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) { $rows[] = $row; }
        $countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM projects WHERE archived_at IS NULL");
        $activeCount = (int)mysqli_fetch_assoc($countResult)["total"];
        api_reply(200, ["ok" => true, "projects" => $rows, "activeCount" => $activeCount]);
    }
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        api_reply(405, ["ok" => false, "message" => "Unsupported request method."]);
    }
    $csrf = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
    if ($csrf === "" || !hash_equals($_SESSION["csrf_token"], $csrf)) {
        api_reply(403, ["ok" => false, "message" => "Your session token expired. Refresh the page and try again."]);
    }
    $input = json_decode(file_get_contents("php://input"), true);
    if (!is_array($input)) { api_reply(400, ["ok" => false, "message" => "Invalid request data."]); }
    $id = filter_var($input["project_id"] ?? null, FILTER_VALIDATE_INT);
    $action = $input["action"] ?? "";
    if (!$id) { api_reply(422, ["ok" => false, "message" => "Invalid submission ID."]); }
    if ($action === "review") {
        $status = $input["status"] ?? "";
        $allowed = ["Submitted", "Reviewed", "Rejected", "Accepted"];
        if (!in_array($status, $allowed, true)) { api_reply(422, ["ok" => false, "message" => "Choose a valid project status."]); }
        $notes = trim((string)($input["admin_notes"] ?? ""));
        $stmt = mysqli_prepare($conn, "UPDATE projects SET status=?, admin_notes=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssi", $status, $notes, $id);
        $message = "Review saved.";
    } elseif ($action === "edit") {
        $title = trim((string)($input["title"] ?? ""));
        $description = trim((string)($input["description"] ?? ""));
        $program = trim((string)($input["program"] ?? ""));
        $calls = trim((string)($input["calls"] ?? ""));
        $duration = ($input["duration"] ?? "") === "" ? null : filter_var($input["duration"], FILTER_VALIDATE_INT);
        $budget = ($input["budget"] ?? "") === "" ? null : filter_var($input["budget"], FILTER_VALIDATE_FLOAT);
        $institution = trim((string)($input["institution"] ?? ""));
        $country = trim((string)($input["country"] ?? ""));
        $contact = trim((string)($input["contact"] ?? ""));
        if ($title === "" || $description === "" || $program === "" || $calls === "" || $duration === false || ($duration !== null && $duration < 1) || $budget === false || ($budget !== null && $budget < 0) || ($contact !== "" && !filter_var($contact, FILTER_VALIDATE_EMAIL))) {
            api_reply(422, ["ok" => false, "message" => "Check the required fields and values."]);
        }
        $stmt = mysqli_prepare($conn, "UPDATE projects SET title=?,duration=?,description=?,budget=?,program=?,calls=?,institution=?,country=?,contact=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sisdsssssi", $title, $duration, $description, $budget, $program, $calls, $institution, $country, $contact, $id);
        $message = "Project information updated.";
    } elseif ($action === "archive") {
        $stmt = mysqli_prepare($conn, "UPDATE projects SET archived_at=NOW() WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id); $message = "Submission archived.";
    } elseif ($action === "restore") {
        $stmt = mysqli_prepare($conn, "UPDATE projects SET archived_at=NULL WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id); $message = "Submission restored.";
    } elseif ($action === "delete") {
        $stmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id); $message = "Submission deleted.";
    } else {
        api_reply(422, ["ok" => false, "message" => "Unknown action."]);
    }
    $ok = mysqli_stmt_execute($stmt);
    $changed = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok || $changed < 0) { api_reply(500, ["ok" => false, "message" => "The submission could not be updated."]); }
    api_reply(200, ["ok" => true, "message" => $message]);
} catch (mysqli_sql_exception $exception) {
    error_log("Submissions API: " . $exception->getMessage());
    api_reply(500, ["ok" => false, "message" => "A database error prevented this action."]);
}
?>
