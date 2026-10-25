<?php
require_once "common.php"; require_once "../db_connection.php";
if($_SERVER["REQUEST_METHOD"]!=="POST"||!admin_csrf_valid()){header("Location: dashboard.php?error=1");exit;}
$id=filter_input(INPUT_POST,"project_id",FILTER_VALIDATE_INT);$action=$_POST["action"]??"";if(!$id){header("Location: dashboard.php?error=1");exit;}
if($action==="review"){$status=$_POST["status"]??"";$allowed=["Submitted","Reviewed","Rejected","Accepted"];$notes=trim($_POST["admin_notes"]??"");if(!in_array($status,$allowed,true)){header("Location: dashboard.php?error=1");exit;}$stmt=mysqli_prepare($conn,"UPDATE projects SET status=?,admin_notes=? WHERE id=?");mysqli_stmt_bind_param($stmt,"ssi",$status,$notes,$id);}
elseif($action==="archive"){$stmt=mysqli_prepare($conn,"UPDATE projects SET archived_at=NOW() WHERE id=?");mysqli_stmt_bind_param($stmt,"i",$id);}
elseif($action==="restore"){$stmt=mysqli_prepare($conn,"UPDATE projects SET archived_at=NULL WHERE id=?");mysqli_stmt_bind_param($stmt,"i",$id);}
elseif($action==="delete"){$stmt=mysqli_prepare($conn,"DELETE FROM projects WHERE id=?");mysqli_stmt_bind_param($stmt,"i",$id);}else{header("Location: dashboard.php?error=1");exit;}
$ok=mysqli_stmt_execute($stmt);mysqli_stmt_close($stmt);if(!$ok){header("Location: dashboard.php?error=1");exit;}$to=$action==="delete"?"dashboard.php?deleted=1":($action==="restore"?"dashboard.php?archived=1&saved=1":"dashboard.php?saved=1");header("Location: $to");exit;
?>
