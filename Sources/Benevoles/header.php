
<?php
session_start();
if(isset($_SESSION['mail'])){
    $mail = $_SESSION['mail'];
    $is_admin = $_SESSION['is_admin'];
    $is_benevole = $_SESSION['is_benevole'];
}
else{
    header('Location: ../login.php');
}

include_once '../database/database.php';
include_once '../checker.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/benevoles_style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../Resources/img/logo/favicon.ico" type="image/x-icon">
    <title>Espace Bénévoles - PétiSign</title>
</head>
<body>

    <nav class="header">
        <a href="index.php">Espace Bénévoles</a>
        <a href="/Sources/" class="benevoles_icon"><img src="/Resources/img/ui_icons/sign-out.png" alt=""></a>
    </nav>

    <?php include_once '../database/database.php' ?>

    
