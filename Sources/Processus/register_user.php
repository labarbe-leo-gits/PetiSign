<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once '../loading.php';

session_start();

include_once '../database/database.php';
include_once 'write_logs.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = htmlspecialchars(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL));
    $username = htmlspecialchars(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
    $password = htmlspecialchars(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
    $confpassword = htmlspecialchars(filter_input(INPUT_POST, 'confpassword', FILTER_SANITIZE_STRING));
    $answer = htmlspecialchars(filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING));
    $verif = htmlspecialchars(filter_input(INPUT_POST, 'verif', FILTER_SANITIZE_NUMBER_INT));
    $original_code = $_SESSION['verification_code'] ?? null;
    $id = htmlspecialchars(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));
    $bday = new dateTime($_POST['anniv']);
    $date= $bday->format('Y-m-d');

    $trimed_username = trim($username);
    $no_spaces_username = preg_replace('/\s+/', '', $trimed_username);

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

    if(empty($verif) || !is_numeric($verif)){
        header('Location: ../register.php');
        exit();
    }

    if ($verif != $original_code) {
        header('Location: ../register.php?error=VerifCode&referer=register');
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

                    
                    $stmt = $pdo->prepare("INSERT INTO USER (email, username, password, birthdate) VALUES (:mail, :username, :password, :bday)");
                    $stmt->bindParam(':mail', $mail);
                    $stmt->bindParam(':username', $no_spaces_username);
                    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                    $stmt->bindParam(':bday', $date);
                    $stmt->execute();

                    session_unset();
                    session_destroy();

                    $ip = $_SERVER['REMOTE_ADDR'];

                    write_logs('../logs/log.txt', 'AUTH03', $username, $ip, 'Nouveau compte utilisateur créé');

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