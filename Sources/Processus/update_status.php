<?php
include_once '../loading.php';
include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: ../login.php');
    exit();
}

$logged_user_id = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$logged_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$logged_user_id->execute();
$logged_user_id = $logged_user_id->fetchColumn();

$new_status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if(empty($new_status) || !$new_status || $new_status == NULL){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if(strlen($new_status) > 60){
    header('Location: ../view_profile.php?id=' . $logged_user_id . '&error=StatTooLong');
    exit();
}

$update_stmt = $pdo->prepare('UPDATE USER SET user_daily_status = :status WHERE id = :id');
$update_stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
$update_stmt->bindParam(':id', $logged_user_id, PDO::PARAM_INT);
$update_stmt->execute();

if($update_stmt->rowCount() > 0){
    header('Location: ../view_profile.php?id=' . $logged_user_id . '&status=Updated');
} else {
    header('Location: ../view_profile.php?id=' . $logged_user_id . '&error=UpdateFailed');
}

?>