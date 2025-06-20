<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include_once '../loading.php';

session_start();

if(!isset($_SESSION['mail'])) {
    //header('Location: ../login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

include_once '../database/database.php';

try{

    $target_id = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

    $get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $get_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
    $get_user_id->execute();
    $user_id = $get_user_id->fetchColumn();

    if($target_id === false || $user_id === false || empty($target_id) || empty($user_id) || !is_numeric($target_id) || !is_numeric($user_id)) {
        echo "Invalid user ID.";
        echo "<script>window.location.href = '../view_profile.php?id=". $target_id ."';</script>";
        exit();
    }

    echo "Target User ID: " . htmlspecialchars($target_id) . "<br>";
    echo "Current User ID: " . htmlspecialchars($user_id) . "<br>";

    $check_if_block_already_exists = $pdo->prepare("SELECT COUNT(*) FROM BLOCKED_USER WHERE id_user = :user_id AND id_blocked_user = :target_id");
    $check_if_block_already_exists->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $check_if_block_already_exists->bindParam(':target_id', $target_id, PDO::PARAM_INT);
    $check_if_block_already_exists->execute();
    $block_exists = $check_if_block_already_exists->fetchColumn();

    if($block_exists != 1){
        echo "Nothing to unblock.";
        echo "<script>window.location.href = '../view_profile.php?id=". $target_id ."';</script>";
        exit();
    }

    $delete_the_block = $pdo->prepare("DELETE FROM BLOCKED_USER WHERE id_user = :user_id AND id_blocked_user = :target_id");
    $delete_the_block->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $delete_the_block->bindParam(':target_id', $target_id, PDO::PARAM_INT);
    $delete_the_block->execute();

    echo "<script>window.location.href = '../view_profile.php?id=". $target_id ."&message=UsrBlckSuccss';</script>";


}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

?>