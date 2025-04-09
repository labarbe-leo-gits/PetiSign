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

    $get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $get_user_id->bindParam(':mail', $username);
    $get_user_id->execute();
    $user_id = $get_user_id->fetchColumn();

    $get_user_ban = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :user_id");
    $get_user_ban->bindParam(':user_id', $user_id);
    $get_user_ban->execute();
    $ban_count = $get_user_ban->fetchColumn();

    if ($ban_count > 0) {

        $_SESSION['ban'] = true;
        $_SESSION['mail'] = $username;

        header("Location: /Sources/ban.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $username);
    $stmt->execute();
    $user = $stmt->fetchColumn();

    $stmt2 = $pdo->prepare("SELECT is_admin FROM USER WHERE email = :mail");
    $stmt2->bindParam(':mail', $username);
    $stmt2->execute();
    $is_admin = $stmt2->fetchColumn();

    $stmt3 = $pdo->prepare("SELECT is_benevole FROM USER WHERE email = :mail");
    $stmt3->bindParam(':mail', $username);
    $stmt3->execute();
    $is_benevole = $stmt3->fetchColumn();

    $_SESSION['mail'] = $username;
    $_SESSION['is_admin'] = $is_admin;
    $_SESSION['is_benevole'] = $is_benevole;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    write_logs('../logs/log.txt', 'INFO', $user, $ip, 'Connexion réussie');

    header("Location: ../profile.php");
} else {
    echo "Mot de passe incorrect ou utilisateur introuvable.";
}
?>