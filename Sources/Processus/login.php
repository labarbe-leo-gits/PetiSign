<?php

include_once '../database/database.php';
include_once 'write_logs.php';
session_start();

$username = $_POST['mail'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT password FROM USER WHERE email = :mail");
$stmt->bindParam(':mail', $username);
$stmt->execute();
$hashedPassword = $stmt->fetchColumn();

if ($hashedPassword && password_verify($password, $hashedPassword)) {
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $username);
    $stmt->execute();
    $user = $stmt->fetchColumn();

    $stmt2 = $pdo->prepare("SELECT is_admin FROM USER WHERE email = :mail");
    $stmt2->bindParam(':mail', $username);
    $stmt2->execute();
    $is_admin = $stmt2->fetchColumn();

    $_SESSION['mail'] = $username;
    $_SESSION['is_admin'] = $is_admin;

    write_logs("../logs/log.txt", "[INFO]", "Nouvelle connexion de l'utilisateur : $user");

    header("Location: ../profile.php");
} else {
    echo "Wrong password or user not found";
}
?>