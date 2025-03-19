<?php

include_once '../database/database.php';

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}
try{
    $user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
    $user_id_stmt->bindParam(':mail', $_SESSION['mail']);
    $user_id_stmt->execute();
    $user_id = $user_id_stmt->fetchColumn();
}catch(PDOException $e){
    echo $e->getMessage();
}

$email_adress = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);
$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
$gender = filter_input(INPUT_POST,'gender', FILTER_SANITIZE_STRING);
$birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

if($email_adress == null && $username == null && $gender == null && $birthdate == null && $description == null){
    header('Location: profile.php');
    exit();
}

$stmt = $pdo->prepare('UPDATE USER SET email = :email, username = :username, gender = :gender, birthdate = :birthdate, description = :description WHERE id = :id');
$stmt->bindParam(':email', $email_adress);
$stmt->bindParam(':id', $user_id);
$stmt->execute();




?>