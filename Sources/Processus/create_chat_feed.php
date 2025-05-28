<?php
include_once '../loading.php';
session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST' || (isset($_GET['create_direct_feed']) && isset($_GET['target_user_id']))) {

    $user_to_contact_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    if(isset($_GET['target_user_id'])) {
        $user_to_contact_id = filter_input(INPUT_GET, 'target_user_id', FILTER_SANITIZE_NUMBER_INT);
    }
    
    $verify_if_user_exist = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE id = :user_id");
    $verify_if_user_exist->bindParam(':user_id', $user_to_contact_id);
    $verify_if_user_exist->execute();
    $user = $verify_if_user_exist->fetchColumn();

    if($user != 1) {
        header('Location: ../error.php?code=333');
        exit();
    }

    $current_user_mail = $_SESSION['mail'];
    $mail_to_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $mail_to_id->bindParam(':mail', $current_user_mail);
    $mail_to_id->execute();
    $user_id = $mail_to_id->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DISCUSSION WHERE (id_user = :user_id AND id_second_user = :second_user_id) OR (id_user = :second_user_id AND id_second_user = :user_id)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':second_user_id', $user_to_contact_id);
    $stmt->execute();
    $discussion_exists = $stmt->fetchColumn();

    if($discussion_exists != 0) {

        $stmt = $pdo->prepare("SELECT id FROM DISCUSSION WHERE (id_user = :user_id AND id_second_user = :second_user_id) OR (id_user = :second_user_id AND id_second_user = :user_id)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':second_user_id', $user_to_contact_id);
        $stmt->execute();
        $discussion_id = $stmt->fetchColumn();
        header('Location: ../chat.php?discussion_id=' . $discussion_id);
        exit();
    }
    
    $insertion = $pdo->prepare("INSERT INTO DISCUSSION (id_user, id_second_user) VALUES (:user_id, :second_user_id)");
    $insertion->bindParam(':user_id', $user_id);
    $insertion->bindParam(':second_user_id', $user_to_contact_id);
    $insertion->execute();

    $discussion_id = $pdo->lastInsertId();

    header('Location: ../chat.php?discussion_id=' . $discussion_id);
    exit();

} else {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit();
}

?>