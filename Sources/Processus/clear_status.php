<?php
include_once '../loading.php';
include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'GET'){
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

$update_stmt = $pdo->prepare('UPDATE USER SET user_daily_status = NULL WHERE id = :id');
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