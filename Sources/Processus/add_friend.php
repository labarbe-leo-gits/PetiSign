<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

include_once '../loading.php';

session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
include_once '../database/database.php';
include_once '../send_notif.php';

try{

    $target_id = filter_input(INPUT_GET, 'uid', FILTER_SANITIZE_NUMBER_INT);

    $get_logged_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $get_logged_user_id->bindParam(':mail', $_SESSION['mail']);
    $get_logged_user_id->execute();
    $logged_user_id = $get_logged_user_id->fetchColumn();

    if($logged_user_id == $target_id){
        header('Location: ../view_profile.php?id=' . $target_id . '&error=OwnFriend');
        exit();
    }

    if(empty($target_id) || !is_numeric($target_id) || $target_id <= 0 || !$target_id) {
        header('Location: ../view_profile.php?id=' . $target_id . '&error=InvalidID');
        exit();
    }

    $check_if_a_request_is_already_pending = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE ((id_user = :logged_user_id AND target_user = :target_id) AND candidate_type = 3 AND current_status = 'En Attente')");
    $check_if_a_request_is_already_pending->bindParam(':logged_user_id', $logged_user_id);
    $check_if_a_request_is_already_pending->bindParam(':target_id', $target_id);
    $check_if_a_request_is_already_pending->execute();
    $request_pending = $check_if_a_request_is_already_pending->fetchColumn();

    if($request_pending > 0){
        header('Location: ../view_profile.php?id=' . $target_id . '&error=AlrPending');
        exit();
    }

    $check_if_friendship_already_exists = $pdo->prepare("SELECT COUNT(*) FROM FRIEND WHERE ((id_user = :logged_user_id AND id_friend = :target_id) OR (id_user = :target_id AND id_friend = :logged_user_id))");
    $check_if_friendship_already_exists->bindParam(':logged_user_id', $logged_user_id);
    $check_if_friendship_already_exists->bindParam(':target_id', $target_id);
    $check_if_friendship_already_exists->execute();
    $friendship_exists = $check_if_friendship_already_exists->fetchColumn();

    if($friendship_exists > 0){
        header('Location: ../view_profile.php?id=' . $target_id . '&error=AlrFriend');
        exit();
    }

    $check_if_request_was_pending_to_current_logged_user = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE id_user = :target_id AND target_user = :logged_user_id AND candidate_type = 3 AND current_status = 'En Attente'");
    $check_if_request_was_pending_to_current_logged_user->bindParam(':target_id', $target_id);
    $check_if_request_was_pending_to_current_logged_user->bindParam(':logged_user_id', $logged_user_id);
    $check_if_request_was_pending_to_current_logged_user->execute();
    $request_pending_to_logged_user = $check_if_request_was_pending_to_current_logged_user->fetchColumn();

    if($request_pending_to_logged_user > 0){

        $accept_friend_request = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Accepté' WHERE id_user = :target_id AND target_user = :logged_user_id AND candidate_type = 3");
        $accept_friend_request->bindParam(':target_id', $target_id);
        $accept_friend_request->bindParam(':logged_user_id', $logged_user_id);
        $accept_friend_request->execute();

        $insert_friendship = $pdo->prepare("INSERT INTO FRIEND (id_user, id_friend) VALUES (:logged_user_id, :target_id)");
        $insert_friendship->bindParam(':logged_user_id', $logged_user_id);
        $insert_friendship->bindParam(':target_id', $target_id);
        $insert_friendship->execute();

        header('Location: ../view_profile.php?id=' . $target_id . '&success=RequestAccepted');
        exit();
    }

    $insert_friend_request = $pdo->prepare("INSERT INTO USER_CANDIDATE (id_user, target_user, candidate_type, current_status) VALUES (:logged_user_id, :target_id, 3, 'En Attente')");
    $insert_friend_request->bindParam(':logged_user_id', $logged_user_id);
    $insert_friend_request->bindParam(':target_id', $target_id);
    $insert_friend_request->execute();

    $target_email_stmt = $pdo->prepare("SELECT email FROM USER WHERE id = :target_id");
    $target_email_stmt->bindParam(':target_id', $target_id);
    $target_email_stmt->execute();
    $target_email = $target_email_stmt->fetchColumn();

    $target_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :target_id");
    $target_username_stmt->bindParam(':target_id', $target_id);
    $target_username_stmt->execute();
    $target_username = $target_username_stmt->fetchColumn();

    $user_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :logged_user_id");
    $user_username_stmt->bindParam(':logged_user_id', $logged_user_id);
    $user_username_stmt->execute();
    $owner_name = $user_username_stmt->fetchColumn();

    $mail_notification_stmt = $pdo->prepare("SELECT mail_notification FROM USER WHERE id = :target_id");
    $mail_notification_stmt->bindParam(':target_id', $target_id);
    $mail_notification_stmt->execute();
    $mail_notification = $mail_notification_stmt->fetchColumn();

    if($mail_notification != 0){
        $mail_sent = new PHPMailer(true);
        EnvoieMail($mail_sent, $target_email, $target_username, "Nouvelle demande d'ami !", "$owner_name vous a envoyé une demande d'ami !", "Vous pouvez l'accepter ou le refuser en vous rendant sur son profil.");
    }

    header('Location: ../view_profile.php?id=' . $target_id . '&success=RequestSent');
    exit();

}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

?>