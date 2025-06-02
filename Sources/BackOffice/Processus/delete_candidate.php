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

        try {

            $get_status_stmt = $pdo->prepare("SELECT current_status FROM USER_CANDIDATE WHERE id = :id");
            $get_status_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $get_status_stmt->execute();
            $status = $get_status_stmt->fetchColumn();

            $stmt = $pdo->prepare("DELETE FROM USER_CANDIDATE WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if($request_type == 1){
                $mail_object = "Votre candidature Bénévole PétiSign";
                $mail_content = "Votre candidature en tant que bénévole a été refusée. Nous vous remercions pour votre intérêt et vous souhaitons le meilleur pour vos projets futurs.";
            }else if($request_type == 2){
                $mail_object = "Votre demande de débanissement";
                $mail_content = "Notre équipe a examiné votre demande de débanissement et a décidé de la refuser. Nous vous remercions pour votre compréhension et vous souhaitons le meilleur pour vos projets futurs.";
            }

            if($status == 'En Attente'){
                $filtered_mail_stmt = $pdo->prepare("SELECT email, username FROM USER WHERE id = :id");
                $filtered_mail_stmt->bindParam(':id', $id_user, PDO::PARAM_INT);
                $filtered_mail_stmt->execute();
                $filtered_mail = $filtered_mail_stmt->fetch(PDO::FETCH_ASSOC);
                $mail = $filtered_mail['email'];

                $filtered_username = filter_var($filtered_mail['username'], FILTER_SANITIZE_STRING);

                $mail_sent = new PHPMailer(true);
                EnvoieMail($mail_sent, $mail, $filtered_username, $mail_object, $mail_content);
            }

            header("Location: ../candidates.php");
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