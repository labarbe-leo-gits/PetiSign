<?php
include_once '../loading.php';
include_once '../database/database.php';

session_start();

if(isset($_GET['action_id']) && $_GET['action_id'] == 1) {
    if(isset($_SESSION['pswd_code_validated']) && $_SESSION['pswd_code_validated'] === true) {
        $new = filter_input(INPUT_POST, 'new', FILTER_SANITIZE_STRING);
        $new_conf = filter_input(INPUT_POST, 'new_conf', FILTER_SANITIZE_STRING);
        $email = $_SESSION['pswd_form_change_email'];
        
        if($new != $new_conf) {
            header("Location: ../forgot_pswd.php?error=password_mismatch");
            exit();
        }

        $newHashedPassword = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE USER SET password = :new WHERE email = :email");
        $stmt->bindParam(':new', $newHashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        unset($_SESSION['pswd_form_change_email']);
        unset($_SESSION['pswd_form_change_code']);
        unset($_SESSION['pswd_code_validated']);

        echo "<script>alert('Mot de passe modifié avec succès !');</script>";
        header("Location: ../login.php");
        exit();
    } else {
        header("Location: ../reset.php?error=unauthorized");
        exit();
    }
} else {
    $old = filter_input(INPUT_POST, 'old', FILTER_SANITIZE_STRING);
    $new = filter_input(INPUT_POST, 'new', FILTER_SANITIZE_STRING);
    $new_conf = filter_input(INPUT_POST, 'new_conf', FILTER_SANITIZE_STRING);
    $mail = $_SESSION['mail'];

    if ($new != $new_conf) {
        echo "<script>window.location.href = '../index.php';</script>";
        exit();
    }

    $stmt = $pdo->prepare("SELECT password FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $mail);
    $stmt->execute();
    $hashedPassword = $stmt->fetchColumn();

    if (password_verify($old, $hashedPassword)) {
        $newHashedPassword = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE USER SET password = :new WHERE email = :mail");
        $stmt->bindParam(':new', $newHashedPassword);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();

        $username_stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
        $username_stmt->bindParam(':mail', $mail);
        $username_stmt->execute();
        $username = $username_stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<script>window.location.href = '../login.php';</script>";
        exit();
    } else {
        echo "<script>window.location.href = '../password_form.php';</script>";
        exit();
    }
}
?>