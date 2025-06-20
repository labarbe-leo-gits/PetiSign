<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: ../login.php');
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
include_once '../database/database.php';
include_once '../loading.php';
include_once '../send_notif.php';


$desired_action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$action_array = ['accept', 'decline', 'cancel', 'remove'];
    
if(empty($desired_action) || !$desired_action || !in_array($desired_action, $action_array)){
    //header('Location: ../manage_friends.php?error=invalid_action');
    echo "<script>window.location.href = '../manage_friends.php?error=invalid_action';</script>";
    exit();
}

if($desired_action == 'accept'){

    $requester_id = filter_input(INPUT_POST, 'requester_id', FILTER_SANITIZE_NUMBER_INT);
    if(empty($requester_id) || !$requester_id || !is_numeric($requester_id)){
        //header('Location: ../manage_friends.php?error=invalid_requester_id');
        echo "<script>window.location.href = '../manage_friends.php?error=invalid_requester_id';</script>";
        exit();
    }

    $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
    if(empty($request_id) || !$request_id || !is_numeric($request_id)){
        //header('Location: ../manage_friends.php?error=invalid_request_id');
        echo "<script>window.location.href = '../manage_friends.php?error=invalid_request_id';</script>";
        exit();
    }

    $get_request_details = $pdo->prepare("SELECT id_user, target_user FROM USER_CANDIDATE WHERE id = :id");
    $get_request_details->bindParam(':id', $request_id, PDO::PARAM_INT);
    $get_request_details->execute();
    $request_details = $get_request_details->fetch(PDO::FETCH_ASSOC);

    $create_friend_link = $pdo->prepare("INSERT INTO FRIEND (id_user, id_friend) VALUES (:id_user, :id_friend)");
    $create_friend_link->bindParam(':id_user', $request_details['id_user'], PDO::PARAM_INT);
    $create_friend_link->bindParam(':id_friend', $request_details['target_user'], PDO::PARAM_INT);
    $create_friend_link->execute();

    $update_request_status = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Accepté' WHERE id = :id");
    $update_request_status->bindParam(':id', $request_id, PDO::PARAM_INT);
    $update_request_status->execute();

    $get_mail_notification_from_id_user = $pdo->prepare("SELECT mail_notification, email, username FROM USER WHERE id = :id");
    $get_mail_notification_from_id_user->bindParam(':id', $request_details['id_user'], PDO::PARAM_INT);
    $get_mail_notification_from_id_user->execute();
    $mail_notification = $get_mail_notification_from_id_user->fetch(PDO::FETCH_ASSOC);

    $initial_target_id = $request_details['target_user'];
    $get_initial_target = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
    $get_initial_target->bindParam(':id', $initial_target_id, PDO::PARAM_INT);
    $get_initial_target->execute();
    $initial_target = $get_initial_target->fetchColumn();

    if($mail_notification != 0){
        $mail_sent = new PHPMailer(true);
        EnvoieMail($mail_sent, $mail_notification['email'], $mail_notification['username'], "Demande d'ami acceptée", "$initial_target a accepté votre demande d'ami !");
    }

    //header('Location: ../manage_friends.php?success=AcceptSuccess');
    echo "<script>window.location.href = '../manage_friends.php?success=AcceptSuccess';</script>";
    exit();
}

if($desired_action == 'decline'){

    $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
    if(empty($request_id) || !$request_id || !is_numeric($request_id)){
        //header('Location: ../manage_friends.php?error=invalid_request_id');
        echo "<script>window.location.href = '../manage_friends.php?error=invalid_request_id';</script>";
        exit();
    }

    $update_request_status = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Rejeté' WHERE id = :id");
    $update_request_status->bindParam(':id', $request_id, PDO::PARAM_INT);
    $update_request_status->execute();

    //header('Location: ../manage_friends.php?success=DeclineSuccess');
    echo "<script>window.location.href = '../manage_friends.php?success=DeclineSuccess';</script>";
    exit();

}

if($desired_action == 'remove'){

    $current_user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $current_user_id_stmt->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
    $current_user_id_stmt->execute();
    $current_user_id = $current_user_id_stmt->fetchColumn();

    $friend_id = filter_input(INPUT_POST, 'friend_id', FILTER_SANITIZE_NUMBER_INT);

    if(empty($friend_id) || !$friend_id || !is_numeric($friend_id)){
        //header('Location: ../manage_friends.php?error=invalid_friend_id');
        echo "<script>window.location.href = '../manage_friends.php?error=invalid_friend_id';</script>";
        exit();
    }

    $count_friend_link = $pdo->prepare("SELECT COUNT(*) FROM FRIEND WHERE (id_user = :id_user AND id_friend = :id_friend) OR (id_user = :id_friend AND id_friend = :id_user)");
    $count_friend_link->bindParam(':id_user', $current_user_id, PDO::PARAM_INT);
    $count_friend_link->bindParam(':id_friend', $friend_id, PDO::PARAM_INT);
    $count_friend_link->execute();
    $friend_link_exists = $count_friend_link->fetchColumn();

    if($friend_link_exists == 0){
        //header('Location: ../manage_friends.php?error=friend_link_not_found');
        echo "<script>window.location.href = '../manage_friends.php?error=friend_link_not_found';</script>";
        exit();
    }

    $delete_friend_link = $pdo->prepare("DELETE FROM FRIEND WHERE (id_user = :id_user AND id_friend = :id_friend) OR (id_user = :id_friend AND id_friend = :id_user)");
    $delete_friend_link->bindParam(':id_user', $current_user_id, PDO::PARAM_INT);
    $delete_friend_link->bindParam(':id_friend', $friend_id, PDO::PARAM_INT);
    $delete_friend_link->execute();

    //header('Location: ../manage_friends.php?success=RemoveSuccess');
    echo "<script>window.location.href = '../manage_friends.php?success=RemoveSuccess';</script>";
    exit();

}

if($desired_action == 'cancel'){

    $request_id = filter_input(INPUT_POST, 'request_id', FILTER_SANITIZE_NUMBER_INT);
    if(empty($request_id) || !$request_id || !is_numeric($request_id)){
        //header('Location: ../manage_friends.php?error=invalid_request_id');
        echo "<script>window.location.href = '../manage_friends.php?error=invalid_request_id';</script>";
        exit();
    }

    $update_request_status = $pdo->prepare("DELETE FROM USER_CANDIDATE WHERE id = :id");
    $update_request_status->bindParam(':id', $request_id, PDO::PARAM_INT);
    $update_request_status->execute();

    //header('Location: ../manage_friends.php?success=CancelSuccess');
    echo "<script>window.location.href = '../manage_friends.php?success=CancelSuccess';</script>";
    exit();

}

?>