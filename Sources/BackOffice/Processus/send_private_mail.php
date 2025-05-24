<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../send_notif.php';

if ($is_admin != 0) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $filtered_user_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        $get_mail_from_id = $pdo->prepare("SELECT email FROM USER WHERE id = :id");
        $get_mail_from_id->bindParam(':id', $filtered_user_id);
        $get_mail_from_id->execute();
        $filtered_mail = $get_mail_from_id->fetchColumn();

        $get_username_from_id = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $get_username_from_id->bindParam(':id', $filtered_user_id);
        $get_username_from_id->execute();
        $filtered_username = $get_username_from_id->fetchColumn();

        $mail_content = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $mail_object = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);

        $mail_sent = new PHPMailer(true);
        EnvoieMail($mail_sent, $filtered_mail, $filtered_username, $mail_object, $mail_content);

        echo "<script>alert('Le mail a bien été envoyé !');</script>";
        header("Location: ../users.php");
        exit();

    } else {
        header("Location: ../create_newsletter.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>