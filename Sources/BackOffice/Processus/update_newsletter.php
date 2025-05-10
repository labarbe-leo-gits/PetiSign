<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if ($is_admin != 0) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $message = nl2br(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));
        $message = str_replace(array("\r\n", "\r", "\n"), '<br />', $message);
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if (!empty($title) && !empty($message)) {
            if (strlen($title) <= 255) {
                try {
                    echo "vous avez cliqué sur le bouton";

                    $update_stmt = $pdo->prepare("UPDATE NEWSLETTER SET title = :title, content = :message WHERE id = :id");
                    $update_stmt->bindParam(':title', $title);
                    $update_stmt->bindParam(':message', $message);
                    $update_stmt->bindParam(':id', $id);
                    $update_stmt->execute();

                    echo "Newsletter updated successfully";

                    //header("Location: ../newsletter.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                header("Location: ../newsletter.php");
                exit();
            }
        } else {
            header("Location: ../newsletter.php");
            exit();
        }
    } else {
        header("Location: ../newsletter.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>