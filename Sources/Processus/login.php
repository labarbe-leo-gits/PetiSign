<?php
include_once '../loading.php';
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

        $get_ban_expiration_date = $pdo->prepare("SELECT expiration FROM BAN WHERE id_user = :user_id");
        $get_ban_expiration_date->bindParam(':user_id', $user_id);
        $get_ban_expiration_date->execute();
        $ban_expiration_date = $get_ban_expiration_date->fetchColumn();

        $current_date = new DateTime();
        $ban_expiration_date = new DateTime($ban_expiration_date);

        if($ban_expiration_date <= $current_date) {
            $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id_user = :user_id");
            $delete_ban->bindParam(':user_id', $user_id);
            $delete_ban->execute();

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

            //$create_session_instance = $pdo->prepare("INSERT INTO SESSION (id_user, ip_address) VALUES (:user_id, :ip_address)");
            //$create_session_instance->bindParam(':user_id', $user_id);
            //$create_session_instance->bindParam(':ip_address', $ip);
            //$create_session_instance->execute();
            
            write_logs('../logs/log.txt', 'AUTH01', $user, $ip, 'Connexion réussie');

            //header("Location: /Sources/profile.php");
            echo "<script>window.location.href = '../view_profile.php?id=" . $user_id . "';</script>";
            exit();
        }

        $_SESSION['ban'] = true;
        $_SESSION['mail'] = $username;

        $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
        $stmt->bindParam(':mail', $username);
        $stmt->execute();
        $user = $stmt->fetchColumn();

        $ip = $_SERVER['REMOTE_ADDR'];

        write_logs('../logs/log.txt', 'AUTH02', $user, $ip, 'Connexion échouée (banni)');
        //header("Location: /Sources/ban.php");
        echo "<script>window.location.href = '../ban.php';</script>";
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

    //$create_session_instance = $pdo->prepare("INSERT INTO SESSION (id_user, ip_address) VALUES (:user_id, :ip_address)");
    //$create_session_instance->bindParam(':user_id', $user_id);
    //$create_session_instance->bindParam(':ip_address', $ip);
    //$create_session_instance->execute();

    $currentTimestamp = date('Y-m-d H:i:s');
    $current_date = new DateTime();

    $current_date = $current_date->format('Y-m-d');

    $update_last_activity = $pdo->prepare("UPDATE USER SET last_activity = :tmp, last_login = :lst_log WHERE id = :mail");
    $update_last_activity->bindParam(':tmp', $currentTimestamp);
    $update_last_activity->bindParam(':lst_log', $current_date);
    $update_last_activity->bindParam(':mail', $user_id);
    $update_last_activity->execute();
    
    write_logs('../logs/log.txt', 'AUTH01', $user, $ip, 'Connexion réussie');

    //header("Location: ../view_profile.php?id=" . $user_id);
    echo "<script>window.location.href = '../view_profile.php?id=" . $user_id . "';</script>";
} else {
    //header('Location: ../login.php?error=WrongCreds&referer=login');
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../login.php';
    echo "<script>window.location.href = '../login.php?error=WrongCreds';</script>";
    exit;
}
?>