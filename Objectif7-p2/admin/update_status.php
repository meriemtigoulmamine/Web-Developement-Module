<?php
session_start();
require_once "../db_connection.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit;
}

$project_id = filter_input(
    INPUT_POST,
    "project_id",
    FILTER_VALIDATE_INT
);

$status = $_POST["status"] ?? "";

$allowed_statuses = [
    "Pending",
    "In Progress",
    "Accepted",
    "Rejected"
];

if (!$project_id || !in_array($status, $allowed_statuses, true)) {
    die("Invalid project ID or status.");
}

$stmt = mysqli_prepare(
    $conn,
    "UPDATE projects SET status = ? WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $project_id
);

if (mysqli_stmt_execute($stmt)) {
    header("Location: dashboard.php");
    exit;
}

echo "Could not update the project status.";

mysqli_stmt_close($stmt);
?>