<?php
include_once '../loading.php';
session_start();
include_once '../database/database.php';

if(!isset($_SESSION['mail'])){
    //header('Location: ../login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

$com_id = htmlspecialchars(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));    

if($_SERVER["REQUEST_METHOD"] != "GET"){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."?code=NoGet';</script>";
    exit();
}

if(!$com_id || !is_numeric($com_id) || empty($com_id)){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."?code=InvalidID';</script>";
    exit();
}

$check_if_id_exist_stmt = $pdo->prepare("SELECT COUNT(id) FROM DISCUSSION WHERE id = :id");
$check_if_id_exist_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$check_if_id_exist_stmt->execute();
$check_if_id_exist = $check_if_id_exist_stmt->fetchColumn();

if(!$check_if_id_exist || $check_if_id_exist == 0){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."?code=NoDiscussion';</script>";
    exit();
}

$logged_user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$logged_user_id_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$logged_user_id_stmt->execute();
$logged_user_id = $logged_user_id_stmt->fetchColumn();

$get_members_stmt = $pdo->prepare("SELECT id_user, id_second_user FROM DISCUSSION WHERE id  = :id");
$get_members_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$get_members_stmt->execute();
$members = $get_members_stmt->fetch(PDO::FETCH_ASSOC);

if($logged_user_id != $members['id_user'] && $logged_user_id != $members['id_second_user']){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."?code=NotAMember';</script>";
    exit();
}

$delete_all_messages_stmt = $pdo->prepare("DELETE FROM MESSAGE WHERE id_discussion = :id");
$delete_all_messages_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$delete_all_messages_stmt->execute();

$delete_discussion_stmt = $pdo->prepare("DELETE FROM DISCUSSION WHERE id = :id");
$delete_discussion_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$delete_discussion_stmt->execute();

echo "<script>window.location.href = '../chat.php?code=Success';</script>";

?>