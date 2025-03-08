<?php

include_once '../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = htmlspecialchars(filter_input(INPUT_POST, 'question', FILTER_SANITIZE_STRING));
    $answer = htmlspecialchars(filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING));

    if (!empty($question) && !empty($answer)) {
        if (strlen($question) <= 255) {
            try {
                $stmt = $pdo->prepare("INSERT INTO CAPTCHA (question, answer) VALUES (:question, :answer)");
                $stmt->bindParam(':question', $question);
                $stmt->bindParam(':answer', $answer);

                $stmt->execute();

                header("Location: ../captcha.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            header("Location: ../add_captcha.php");
            exit();
        }
    } else {
        header("Location: ../add_captcha.php");
        exit();
    }
} else {
    header("Location: ../add_captcha.php");
    exit();
}

?>