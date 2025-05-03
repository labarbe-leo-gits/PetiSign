<?php
include_once '../loading.php';
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
$skin = filter_input(INPUT_POST, 'skin', FILTER_SANITIZE_NUMBER_INT);

$eyes_color = filter_input(INPUT_POST, 'eyes_color', FILTER_SANITIZE_NUMBER_INT);
$mouth_color = filter_input(INPUT_POST, 'mouth_color', FILTER_SANITIZE_NUMBER_INT);
$hat_color = filter_input(INPUT_POST, 'hat_color', FILTER_SANITIZE_NUMBER_INT);
$skin_color = filter_input(INPUT_POST, 'skin_color', FILTER_SANITIZE_NUMBER_INT);

$id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$id_stmt->bindParam(':mail', $_SESSION['mail']);
$id_stmt->execute();
$id = $id_stmt->fetchColumn();

try{
    $stmt = $pdo->prepare('UPDATE USER SET avatar_eyes = :eyes, avatar_mouth = :mouth, avatar_hat = :hat, avatar_skin = :skin, avatar_eyes_color = :eyes_color, avatar_mouth_color = :mouth_color, avatar_hat_color = :hat_color, avatar_skin_color = :skin_color WHERE id = :id');
    $stmt->bindParam(':eyes', $eyes);
    $stmt->bindParam(':mouth', $mouth);
    $stmt->bindParam(':hat', $hat);
    $stmt->bindParam(':skin', $skin);
    $stmt->bindParam(':eyes_color', $eyes_color);
    $stmt->bindParam(':mouth_color', $mouth_color);
    $stmt->bindParam(':hat_color', $hat_color);
    $stmt->bindParam(':skin_color', $skin_color);

    $stmt->bindParam(':id', $id);
    $stmt->execute();
    header('Location: ../profile.php');
    exit;
}catch(Exception $e){
    echo $e->getMessage();
    exit;
}



?>