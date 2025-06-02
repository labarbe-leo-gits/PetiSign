<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../send_notif.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $id_user = intval($_GET['user_id']);
        $request_type = filter_input(INPUT_GET, 'request_type', FILTER_VALIDATE_INT);

        if(empty($id) || empty($id_user) || empty($request_type) || !in_array($request_type, [1, 2])) {
            header("Location: ../candidates.php?error=invalid_parameters");
            exit();
        }

        if($request_type == 1){
            $mail_object = "Votre candidature Bénévole PétiSign";
            $mail_content = "Votre candidature en tant que bénévole a été acceptée. Vous pouvez dès à présent vous connecter à votre compte pour participer aux activités bénévole, créer votre équipe et vous inscrire aux événements.";
        }else if($request_type == 2){
            $mail_object = "Votre demande de débanissement";
            $mail_content = "Notre équipe a examiné votre demande de débanissement et a décidé de l'accepter. Vous pouvez dès à présent vous connecter à votre compte.";
        }

        try {
            $stmt = $pdo->prepare("UPDATE USER_CANDIDATE SET current_status = 'Accepté' WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if($request_type == 1){
                $set_benevole_to_user = $pdo->prepare("UPDATE USER SET is_benevole = 1 WHERE id = :id");
                $set_benevole_to_user->bindParam(':id', $id_user, PDO::PARAM_INT);
                $set_benevole_to_user->execute();
            }

            if($request_type == 2){
                $get_user_ban_id = $pdo->prepare("SELECT id FROM BAN WHERE id_user = :user_id");
                $get_user_ban_id->bindParam(':user_id', $id_user, PDO::PARAM_INT);
                $get_user_ban_id->execute();
                $ban_id = $get_user_ban_id->fetchColumn();

                if($ban_id){
                    $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id = :id");
                    $delete_ban->bindParam(':id', $ban_id, PDO::PARAM_INT);
                    $delete_ban->execute();
                }
            }

            $filtered_mail_stmt = $pdo->prepare("SELECT email, username FROM USER WHERE id = :id");
            $filtered_mail_stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
            $filtered_mail_stmt->execute();
            $filtered_mail = $filtered_mail_stmt->fetch(PDO::FETCH_ASSOC);
            $mail = $filtered_mail['email'];

            $filtered_username = filter_var($filtered_mail['username'], FILTER_SANITIZE_STRING);

            $mail_sent = new PHPMailer(true);
            EnvoieMail($mail_sent, $mail, $filtered_username, $mail_object, $mail_content);

            header("Location: ../candidates.php?success=accept_candidate&referer=admin_validation");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../candidates.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>