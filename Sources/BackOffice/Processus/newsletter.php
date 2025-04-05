<?php

include_once '../../database/database.php';
include_once 'security.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once '../../SendNewsletterFunction.php';

if ($is_admin != 0) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $message = nl2br(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

        if (!empty($title) && !empty($message)) {
            if (strlen($title) <= 255) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO NEWSLETTER (title, content) VALUES (:title, :content)");
                    $stmt->bindParam(':title', $title);
                    $stmt->bindParam(':content', $message);
                    $stmt->execute();

                    $newsletter_id_stmt = $pdo->prepare("SELECT id FROM NEWSLETTER WHERE title = :title");
                    $newsletter_id_stmt->bindParam(':title', $title);
                    $newsletter_id_stmt->execute();
                    $newsletter_id = $newsletter_id_stmt->fetchColumn();

                    $users = $pdo->prepare("SELECT id, email, username FROM USER WHERE newsletter = 1");
                    $users->execute();
                    $users_data = $users->fetchAll(PDO::FETCH_ASSOC);

                    $mail_sent = new PHPMailer(true);

                    foreach($users_data as $user){
                        EnvoieMail($mail_sent, $user['email'], $user['username'], $title, $message);
                        $abonnement_stmt = $pdo->prepare("INSERT INTO ABONNEMENT (id_user, id_newsletter) VALUES (:id_user, :id_newsletter)");
                        $abonnement_stmt->bindParam(':id_user', $user['id']);
                        $abonnement_stmt->bindParam(':id_newsletter', $newsletter_id);
                        $abonnement_stmt->execute();
                    }

                    header("Location: ../newsletter.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                header("Location: ../create_newsletter.php");
                exit();
            }
        } else {
            header("Location: ../create_newsletter.php");
            exit();
        }
    } else {
        header("Location: ../create_newsletter.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>