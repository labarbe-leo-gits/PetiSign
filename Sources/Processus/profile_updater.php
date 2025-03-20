<?php

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

if($mail == null || $mail == false || $username == null || $username == false || $gender == null || $gender == false || $description == null || $description == false || $birthdate == null || $birthdate == false){
    header("Location: ../profile.php");
    exit();
}

$id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$id_stmt->bindParam(':mail', $_SESSION['mail']);
$id_stmt->execute();
$id = $id_stmt->fetchColumn();

try {
    $stmt = $pdo->prepare("UPDATE USER SET email = :mail, description = :description, gender = :gender, birthdate = :bday, username = :username WHERE id = :id");
    $stmt->bindParam(':mail', $mail);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':bday', $birthdate);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':id', $id);
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