<?php
require_once "common.php";require_once "../db_connection.php";
if($_SERVER["REQUEST_METHOD"]!=="POST"||!admin_csrf_valid()){header("Location: dashboard.php?error=1");exit;}
$id=filter_input(INPUT_POST,"project_id",FILTER_VALIDATE_INT);$status=$_POST["status"]??"";$allowed=["Submitted","Reviewed","Rejected","Accepted"];
if(!$id||!in_array($status,$allowed,true)){header("Location: dashboard.php?error=1");exit;}
$notes=trim($_POST["admin_notes"]??"");$stmt=mysqli_prepare($conn,"UPDATE projects SET status=?,admin_notes=? WHERE id=?");mysqli_stmt_bind_param($stmt,"ssi",$status,$notes,$id);$ok=mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);header("Location: dashboard.php?".($ok?"saved=1":"error=1"));exit;
?>
