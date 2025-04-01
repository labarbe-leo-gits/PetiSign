<?php
session_start();
if(isset($_SESSION['mail'])){
    $mail = $_SESSION['mail'];
    
    $is_admin = $_SESSION['is_admin'];
    $is_benevole = $_SESSION['is_benevole'];

    if($is_admin == 0){
        header('Location: ../error.php?code=403');
    }

}
else{
    header('Location: ../login.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/benevoles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../Resources/img/logo/favicon.ico" type="image/x-icon">
    <title>Espace Bénévoles - PétiSign</title>
</head>
<body>
    <nav class="header">
        <a>Espace Bénévoles</a>
        <div class="sign-out">
            <a href="index.php">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/sign-out.png" alt="Retourner sur PétiSign">
            </a>
        </div>
    </nav>

    <?php include_once '../database/database.php' ?>
