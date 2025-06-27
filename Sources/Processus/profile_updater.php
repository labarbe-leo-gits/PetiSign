<?php
include_once '../loading.php';
include_once '../database/database.php';

session_start();

if(!isset($_SESSION['mail'])){
    //header('Location: login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

if($_SERVER['REQUEST_METHOD'] != 'POST'){
    //header('Location: ../profile.php');
    echo "<script>window.location.href = '../profile.php';</script>";
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
    //header("Location: ../profile.php");
    echo "<script>window.location.href = '../profile.php?error=InvalidInput';</script>";
    exit();
}

$id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$id_stmt->bindParam(':mail', $_SESSION['mail']);
$id_stmt->execute();
$id = $id_stmt->fetchColumn();

$eighteen_years_ago = date('Y-m-d', strtotime('-18 years'));

if($birthdate > $eighteen_years_ago){
    //header("Location: ../profile.php?error=AgeTooYoung");
    echo "<script>window.location.href = '../profile.php?error=AgeTooYoung';</script>";
    exit();
}

try {

    $username_lower = mb_strtolower($username, 'UTF-8');

    $filename = '../json/banned_username.json';
    if (file_exists($filename)) {
        $json = file_get_contents($filename);
        $data = json_decode($json, true);
    } else {
        echo "File not found.";
        exit;
    }

    if (isset($data['banned_usernames'])) {
        $banned_usernames = $data['banned_usernames'];
        $lower_all_banned_usernames = array_map('mb_strtolower', $banned_usernames);
        if (in_array($username_lower, $lower_all_banned_usernames)) {
            //header('Location: register.php?error=BannedUsername&referer=mail_verification');
            echo "<script>window.location.href = '../profile.php?error=BannedUsername';</script>";
            exit;
        }    

        foreach ($lower_all_banned_usernames as $banned_username) {
            if (str_contains($username_lower, $banned_username)) {
                //header('Location: register.php?error=BannedUsername&referer=mail_verification');
                echo "<script>window.location.href = '../profile.php?error=BannedUsername';</script>";
                exit;
            }
        }
    }

    $check_if_username_exists = $pdo->prepare("SELECT id FROM USER WHERE username = :username AND id != :id");
    $check_if_username_exists->bindParam(':username', $username);
    $check_if_username_exists->bindParam(':id', $id);
    $check_if_username_exists->execute();
    $existing_user = $check_if_username_exists->fetchColumn();

    if($existing_user){
        //header("Location: ../profile.php?error=UsernameExists");
        echo "<script>window.location.href = '../profile.php?error=UsrAlreadyExists';</script>";
        exit();
    }

    $check_if_email_exists = $pdo->prepare("SELECT id FROM USER WHERE email = :mail AND id != :id");
    $check_if_email_exists->bindParam(':mail', $mail);
    $check_if_email_exists->bindParam(':id', $id);
    $check_if_email_exists->execute();
    $existing_email = $check_if_email_exists->fetchColumn();

    if($existing_email){
        //header("Location: ../profile.php?error=EmailExists");
        echo "<script>window.location.href = '../profile.php?error=EmailAlreadyExists';</script>";
        exit();
    }

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

    //header("Location: ../profile.php");
    echo "<script>window.location.href = '../profile.php';</script>";
    exit();
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

?>