<?php

include_once '../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = htmlspecialchars(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));
    $message = htmlspecialchars(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

    if (!empty($title) && !empty($message)) {
        if (strlen($title) <= 255) {
            try {
                $stmt = $pdo->prepare("INSERT INTO NEWSLETTER (title, content) VALUES (:title, :content)");
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':content', $message);
                $stmt->execute();

                $get_users_mails = $pdo->prepare("SELECT email FROM USER");
                $get_users_mails->execute();
                $users_mails = $get_users_mails->fetchAll(PDO::FETCH_ASSOC);

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

?>