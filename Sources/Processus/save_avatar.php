<?php

session_start();
if(!isset($_SESSION['mail'])){
    header('Location: ../index.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../index.php');
    exit;
}

include_once '../database/database.php';

$eyes = filter_input(INPUT_POST, 'eyes', FILTER_SANITIZE_NUMBER_INT);
$mouth = filter_input(INPUT_POST, 'mouth', FILTER_SANITIZE_NUMBER_INT);
$hat = filter_input(INPUT_POST, 'hat', FILTER_SANITIZE_NUMBER_INT);

$id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$id_stmt->bindParam(':mail', $_SESSION['mail']);
$id_stmt->execute();
$id = $id_stmt->fetchColumn();

try{
    $stmt = $pdo->prepare('UPDATE USER SET avatar_eyes = :eyes, avatar_mouth = :mouth, avatar_hat = :hat WHERE id = :id');
    $stmt->bindParam(':eyes', $eyes);
    $stmt->bindParam(':mouth', $mouth);
    $stmt->bindParam(':hat', $hat);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header('Location: ../profile.php');
    exit;
}catch(Exception $e){
    echo $e->getMessage();
    exit;
}



?>