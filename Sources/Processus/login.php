<?php

include_once '../database/database.php';
session_start();

$username = $_POST['mail'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT password FROM USER WHERE email = :mail");
$stmt->bindParam(':mail', $username);
$stmt->execute();
$hashedPassword = $stmt->fetchColumn();

if ($hashedPassword && password_verify($password, $hashedPassword)) {
    // retrieves username from database and echo hello [user]
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $username);
    $stmt->execute();
    $user = $stmt->fetchColumn();
    echo "Hello " . $user;
    $_SESSION['mail'] = $username;
} else {
    echo "Wrong password or user not found";
}
?>