<?php
include_once '../loading.php';
include_once '../database/database.php';

session_start();

if(!isset($_SESSION['mail'])){
    //header('Location: login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

$old = filter_input(INPUT_POST, 'old', FILTER_SANITIZE_STRING);
$new = filter_input(INPUT_POST, 'new', FILTER_SANITIZE_STRING);
$new_conf = filter_input(INPUT_POST, 'new_conf', FILTER_SANITIZE_STRING);
$mail = $_SESSION['mail'];

if ($new != $new_conf) {
    //header("Location: ../password_form.php");
    echo "<script>window.location.href = '../password_form.php';</script>";
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
    //header("Location: ../profile.php");
    echo "<script>window.location.href = '../profile.php';</script>";
    exit();
} else {
    //header("Location: ../password_form.php");
    echo "<script>window.location.href = '../password_form.php';</script>";
    exit();
}
?>