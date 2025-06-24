<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try{
        $mail = htmlspecialchars(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL));
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

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
                $stmt = $pdo->prepare("INSERT INTO USER (email, username, password, is_admin) VALUES (:mail, :username, :password, :is_admin)");
                $stmt->bindParam(':mail', $mail);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                $is_admin = 1;
                $stmt->bindParam(':is_admin', $is_admin);
                $stmt->execute();

                header('Location: ../users.php?success=UsrCreated&referer=admin_create');
                exit();
            } else {
                header('Location: '. $_SERVER['HTTP_REFERER']);
                exit();
            }
        } else {
            header('Location: '. $_SERVER['HTTP_REFERER']);
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>