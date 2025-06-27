<?php
include_once '../loading.php';
include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    //header('Location: ' . $_SERVER['HTTP_REFERER']);
    echo "<script>window.location.href = '../view_profile.php?error=InvalidRequestMethod';</script>";
    exit();
}

session_start();

if(!isset($_SESSION['mail'])){
    //header('Location: ../login.php');
    echo "<script>window.location.href = '../login.php?error=NotLoggedIn';</script>";
    exit();
}

$logged_user_id = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$logged_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$logged_user_id->execute();
$logged_user_id = $logged_user_id->fetchColumn();

$new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$new_status = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $new_status);

if(empty($new_status) || !$new_status || $new_status == NULL){
    //header('Location: ' . $_SERVER['HTTP_REFERER']);
    header('Location: ../view_profile.php?id=' . $logged_user_id . '&error=EmptyStatus');
    exit();
}

if(strlen($new_status) > 60){
    //header('Location: ../view_profile.php?id=' . $logged_user_id . '&error=StatTooLong');
    echo "<script>window.location.href = '../view_profile.php?id=" . $logged_user_id . "&error=StatTooLong';</script>";
    exit();
}

$update_stmt = $pdo->prepare('UPDATE USER SET user_daily_status = :status WHERE id = :id');
$update_stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
$update_stmt->bindParam(':id', $logged_user_id, PDO::PARAM_INT);
$update_stmt->execute();

if($update_stmt->rowCount() > 0){
    //header('Location: ../view_profile.php?id=' . $logged_user_id . '&status=Updated');
    echo "<script>window.location.href = '../view_profile.php?id=" . $logged_user_id . "&status=Updated';</script>";
} else {
    //header('Location: ../view_profile.php?id=' . $logged_user_id . '&error=UpdateFailed');
    echo "<script>window.location.href = '../view_profile.php?id=" . $logged_user_id . "&error=UpdateFailed';</script>";
}

?>