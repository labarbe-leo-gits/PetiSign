<?php
include_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = htmlspecialchars(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL));
    $username = htmlspecialchars(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = htmlspecialchars(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    $confpassword = htmlspecialchars(filter_input(INPUT_POST, 'confpassword', FILTER_SANITIZE_STRING));
    $answer = htmlspecialchars(filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING));
    $id = htmlspecialchars(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));

    $stmt = $pdo->prepare("SELECT * FROM CAPTCHA WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $captcha = $stmt->fetch();

    if ($captcha) {
        echo "Captcha fetched: " . htmlspecialchars($captcha['answer'], ENT_QUOTES, 'UTF-8');
    } else {
        header('Location: ../error.php?code=808');
        exit();
    }

    if ($captcha && $captcha['answer'] === $answer) {
        if ($password === $confpassword) {
            $stmt = $pdo->prepare("SELECT * FROM USER WHERE email = :mail");
            $stmt->bindParam(':mail', $mail);
            $stmt->execute();
            $user = $stmt->fetch();

            if (!$user) {
                $stmt = $pdo->prepare("SELECT * FROM USER WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $user = $stmt->fetch();

                if (!$user) {
                    $stmt = $pdo->prepare("INSERT INTO USER (email, username, password) VALUES (:mail, :username, :password)");
                    $stmt->bindParam(':mail', $mail);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                    $stmt->execute();

                    header('Location: ../login.php');
                    exit();
                } else {
                    header('Location: ../register.php');
                    exit();
                }
            } else {
                header('Location: ../register.php');
                exit();
            }
        } else {
            header('Location: ../register.php');
            exit();
        }
    } else {
        header('Location: ../register.php');
        exit();
    }
}
?>