<?php
session_start();
require_once "db_connection.php";

// Only a signed-in researcher can submit a project.
if (!isset($_SESSION["user_id"]) || isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["submit_project"])) {
    header("Location: index.php");
    exit;
}

$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$duration = ($_POST["duration"] ?? "") !== "" ? filter_var($_POST["duration"], FILTER_VALIDATE_INT) : null;
$budget = ($_POST["budget"] ?? "") !== "" ? filter_var($_POST["budget"], FILTER_VALIDATE_FLOAT) : null;
$program = trim($_POST["program"] ?? "");
$calls = trim($_POST["calls"] ?? "");
$institution = trim($_POST["institution"] ?? "");
$country = trim($_POST["country"] ?? "");
$contact = trim($_POST["contact"] ?? "");
$needs = isset($_POST["needs"]) && is_array($_POST["needs"]) ? implode(", ", $_POST["needs"]) : "";
$other_details = trim($_POST["other"] ?? "");

if ($title === "" || $description === "" || $duration === false || ($duration !== null && $duration < 1) || $budget === false || ($budget !== null && $budget < 0)) {
    header("Location: index.php?error=invalid");
    exit;
}

$sql = "INSERT INTO projects
    (user_id, title, duration, description, budget, program, calls,
     institution, country, contact, needs, other_details)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
$user_id = (int)$_SESSION["user_id"];
mysqli_stmt_bind_param($stmt, "isisdsssssss", $user_id, $title, $duration,
    $description, $budget, $program, $calls, $institution, $country, $contact, $needs, $other_details);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: index.php?submitted=1#home");
    exit;
}
mysqli_stmt_close($stmt);
header("Location: index.php?error=save");
exit;
?>