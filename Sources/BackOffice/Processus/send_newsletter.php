<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../SendNewsletterFunction.php';

if ($is_admin != 0) {

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        $newsletter_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $filtered_id = filter_var($newsletter_id, FILTER_SANITIZE_NUMBER_INT);

        $get_current_status = $pdo->prepare("SELECT status FROM NEWSLETTER WHERE id = :id");
        $get_current_status->bindParam(':id', $filtered_id);
        $get_current_status->execute();
        $current_status = $get_current_status->fetchColumn();

        if ($current_status == 1) {
            header("Location: ../newsletter.php");
            exit();
        }

        $title = $pdo->prepare("SELECT title FROM NEWSLETTER WHERE id = :id");
        $title->bindParam(':id', $filtered_id);
        $title->execute();
        $title = $title->fetchColumn();

        $content = $pdo->prepare("SELECT content FROM NEWSLETTER WHERE id = :id");
        $content->bindParam(':id', $filtered_id);
        $content->execute();
        $content = $content->fetchColumn();

        $users_mails_and_username = $pdo->prepare("SELECT id, email, username FROM USER WHERE newsletter = 1");
        $users_mails_and_username->execute();
        $users_mails_and_username = $users_mails_and_username->fetchAll(PDO::FETCH_ASSOC);

        $mail_sent = new PHPMailer(true);

        foreach ($users_mails_and_username as $user) {
            EnvoieMail($mail_sent, $user['email'], $user['username'], $title, $content, "abonné à notre newsletter.");
            $abonnement_stmt = $pdo->prepare("INSERT INTO ABONNEMENT (id_user, id_newsletter) VALUES (:id_user, :id_newsletter)");
            $abonnement_stmt->bindParam(':id_user', $user['id']);
            $abonnement_stmt->bindParam(':id_newsletter', $filtered_id);
            $abonnement_stmt->execute();
        }

        $update_stmt = $pdo->prepare("UPDATE NEWSLETTER SET status = 1 WHERE id = :id");
        $update_stmt->bindParam(':id', $filtered_id);
        $update_stmt->execute();

        header("Location: ../newsletter.php");
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