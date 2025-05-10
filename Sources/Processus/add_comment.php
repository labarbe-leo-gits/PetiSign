<?php

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
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $petition_id = filter_input(INPUT_POST, 'petition_id', FILTER_SANITIZE_NUMBER_INT);

    $comment_length = strlen($comment);

    if($comment_length > 200) {
        echo "Comment is too long!";
        exit();
    }

    $petition_owner_stmt = $pdo->prepare("SELECT user FROM PETITION WHERE id = :petition_id");
    $petition_owner_stmt->bindParam(':petition_id', $petition_id);
    $petition_owner_stmt->execute();
    $petition_owner = $petition_owner_stmt->fetchColumn();

    $owner_mail = $pdo->prepare("SELECT email FROM USER WHERE id = :owner_id");
    $owner_mail->bindParam(':owner_id', $petition_owner);
    $owner_mail->execute();
    $owner_email = $owner_mail->fetchColumn();

    $owner_name = $pdo->prepare("SELECT username FROM USER WHERE id = :owner_id");
    $owner_name->bindParam(':owner_id', $petition_owner);
    $owner_name->execute();
    $owner_name = $owner_name->fetchColumn();

    $username_user = $pdo->prepare("SELECT username FROM USER WHERE id = :user_id");
    $username_user->bindParam(':user_id', $user_id);
    $username_user->execute();
    $username_user = $username_user->fetchColumn();

    $petition_title = $pdo->prepare("SELECT title FROM PETITION WHERE id = :petition_id");
    $petition_title->bindParam(':petition_id', $petition_id);
    $petition_title->execute();
    $petition_title = $petition_title->fetchColumn();
}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

if(empty($comment) || !is_numeric($user_id) || !is_numeric($petition_id) || !$comment) {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit();
}	

try{

    $stmt = $pdo->prepare("INSERT INTO COMMENT (id_user, id_target, content, target_type) VALUES (:user_id, :petition_id, :comment, 1)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':petition_id', $petition_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->execute();

    $mail_sent = new PHPMailer(true);

    echo "Comment added successfully!";
    EnvoieMail($mail_sent, $owner_email, $owner_name, "Nouveau commentaire !", "$username_user a commenté votre pétition : $petition_title \nVous pouvez le consulter ici : http://5.196.4.238/Sources/view_petition.php?id=$petition_id");
    header('Location: ../view_petition.php?id=' . $petition_id);

}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

?>