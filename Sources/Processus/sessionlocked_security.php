<?php

session_start();
include_once 'database/database.php';

$check_if_account_still_exists = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE email = :mail");
$check_if_account_still_exists->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$check_if_account_still_exists->execute();
$account_exists = $check_if_account_still_exists->fetchColumn();
if($account_exists == 0){
    header('Location: error.php?code=403');
    exit();
}

$get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_user_id->execute();
$user_id = $get_user_id->fetchColumn();

$check_if_user_got_banned = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :user_id");
$check_if_user_got_banned->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$check_if_user_got_banned->execute();
$ban_exists = $check_if_user_got_banned->fetchColumn();

if($ban_exists > 0){
    if(!isset($_SESSION['ban'])){
        $_SESSION['ban'] = true;
        header("Location: /Sources/ban.php");
        exit();
    }
}

// todo : unban when manually unbanned

?>