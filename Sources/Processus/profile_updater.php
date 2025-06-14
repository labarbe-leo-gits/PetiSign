<?php
include_once '../loading.php';
include_once '../database/database.php';

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    header('Location: ../profile.php');
    exit();
}

$original_mail = $_SESSION['mail'];
$mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$gender = $_POST['gender'];
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$birthdate = $_POST['anniv'];
$newsletter = isset($_POST['newsletter']) ? 1 : 0;
$mails_notif = isset($_POST['mails_notif']) ? 1 : 0;
$profile_status = isset($_POST['profile_status']) ? 1 : 0;

if($mail == null || $mail == false || $username == null || $username == false || $gender == null || $gender == false || $description == null || $description == false || $birthdate == null || $birthdate == false){
    header("Location: ../profile.php");
    exit();
}

$id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$id_stmt->bindParam(':mail', $_SESSION['mail']);
$id_stmt->execute();
$id = $id_stmt->fetchColumn();

$eighteen_years_ago = date('Y-m-d', strtotime('-18 years'));

if($birthdate > $eighteen_years_ago){
    header("Location: ../profile.php?error=AgeTooYoung");
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE USER SET email = :mail, description = :description, gender = :gender, birthdate = :bday, username = :username, newsletter = :news, mail_notification = :mails_notif, user_public = :pb_usr WHERE id = :id");
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':bday', $birthdate);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':news', $newsletter);
    $stmt->bindParam(':mails_notif', $mails_notif);
    $stmt->bindParam(':pb_usr', $profile_status);
    $stmt->execute();

    if($mail != $original_mail){
        $_SESSION['mail'] = $mail;
    }

    header("Location: ../profile.php");
    exit();
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>