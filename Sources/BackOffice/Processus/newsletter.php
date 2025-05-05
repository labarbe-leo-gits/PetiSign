<?php
include_once '../../loading.php';
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