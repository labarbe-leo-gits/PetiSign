<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_STRING);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING);

        if (!empty($id) && !empty($question) && !empty($answer)) {
            try {
                $stmt = $pdo->prepare("UPDATE CAPTCHA SET question = :question, answer = :answer WHERE id = :id");
                $stmt->bindParam(':question', $question);
                $stmt->bindParam(':answer', $answer);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                header("Location: ../captcha.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            header("Location: ../modify_captcha_form.php?id=$id");
            exit();
        }
    } else {
        header("Location: ../captcha.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>