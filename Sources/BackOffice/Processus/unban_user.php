<?php

ini_set('display_errors','1');
ini_set('display_startup_errors','1');
error_reporting(E_ALL);

include_once '../../loading.php';
include_once 'security.php';
include_once '../../database/database.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../send_notif.php';

if($is_admin != 0){

    $get_user_ban_id = $pdo->prepare("SELECT id FROM BAN WHERE id_user = :user_id");
    $get_user_ban_id->bindParam(':user_id', $_GET['id']);
    $get_user_ban_id->execute();
    $ban_id = $get_user_ban_id->fetchColumn();

    $user_info = $pdo->prepare("SELECT username, email FROM USER WHERE id = :id");
    $user_info->bindParam(':id', $_GET['id']);
    $user_info->execute();
    $user_data = $user_info->fetch(PDO::FETCH_ASSOC);
    $filtered_username = htmlspecialchars($user_data['username']);
    $mail = htmlspecialchars($user_data['email']);

    if($ban_id){

        $check_if_a_request_was_pending = $pdo->prepare("SELECT COUNT(id) FROM USER_CANDIDATE WHERE id_user = :id AND candidate_type = 2 AND current_status = 'En Attente'");
        $check_if_a_request_was_pending->bindParam(':id', $_GET['id']);
        $check_if_a_request_was_pending->execute();
        $request_pending = $check_if_a_request_was_pending->fetchColumn();

        $mail_object = "Votre demande de débanissement";
        $mail_content = "Notre équipe a examiné votre demande de débanissement et a décidé de l'accepter. Vous pouvez dès à présent vous connecter à votre compte.";


        $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id_user = :id");
        $delete_ban->bindParam(':id', $_GET['id']);
        $delete_ban->execute();

        if($request_pending > 0){
	    $stmt = $pdo->prepare("SELECT id FROM USER_CANDIDATE WHERE id_user = :usr_id");
	    $stmt->bindParam(':usr_id', $_GET['id']);
	    $stmt->execute();
	    $req_id = $stmt->fetchColumn();

            $mail_sent = new PHPMailer(true);
            EnvoieMail($mail_sent, $mail, $filtered_username, $mail_object, $mail_content);
	    $update_stmt = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Accepté' WHERE id = :request_id");
	    $update_stmt->bindParam(':request_id',$req_id);
	    $update_stmt->execute();
        }
        
        header("Location: ../users.php?success=UnbanSuccess&referer=admin");
        exit;
    }
}else{
    header("Location: /Sources/error.php?code=403");
    exit;
}


?>
