
<?php
session_start();
if(isset($_SESSION['mail'])){
    $mail = $_SESSION['mail'];
    
    

    $is_admin = $_SESSION['is_admin'];
    $is_benevole = $_SESSION['is_benevole'];

}
else{
    header('Location: ../error.php?code=403');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/backoffice_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../Resources/img/logo/favicon.ico" type="image/x-icon">
    <title>Back Office - PétiSign</title>
</head>
<body>

    <nav class="header">
        <a href="index.php">Back Office</a>
    </nav>

    <div class="back_container">
        <div class="left_panel">
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/database.png" alt="">
                <div class="space">●</div>
                <a href="database_gestion.php" class="navigation_menu_item">Gestion de la Base de Données (BDD)</a>
            </div>
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/unlogged_user.png" alt="">
                <div class="space">●</div>
                <a href="users.php" class="navigation_menu_item">Gestion des utilisateurs</a>
            </div>
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/team.png" alt="">
                <div class="space">●</div>
                <a href="teams.php" class="navigation_menu_item">Gestion des équipes bénévoles</a>
            </div>
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/captcha.png" alt="">
                <div class="space">●</div>
                <a href="captcha.php" class="navigation_menu_item">Captcha</a>
            </div>
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/newsletter.png" alt="">
                <div class="space">●</div>
                <a href="newsletter.php" class="navigation_menu_item">Newsletter</a>
            </div>
            <div class="item">
                <img class="back_office_nav_img" src="../../Resources/img/ui_icons/log.png" alt="">
                <div class="space">●</div>
                <a href="logs.php" class="navigation_menu_item">Logs</a>
            </div>
            <div class="item" id="exit_btn_container">
                <button class="custom-button" id="exit_btn" onclick="window.location.href = '../index.php'">Retourner sur PétiSign</button>
            </div>
        </div>
    <?php include_once '../database/database.php' ?>

    
