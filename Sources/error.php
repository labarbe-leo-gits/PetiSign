<?php


if(isset($_GET['code'])){
    $json_file = file_get_contents('json/error_manager.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['code'], $error_manager)){
        $insertVal = $_GET['code'];
    } else {
        $insertVal = " ";
    }
} else {
    header('location: index.php');
    exit();
}

$error_details = $error_manager[$insertVal];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FED78B">
    <meta name="description" content="PétiSign est une plateforme de pétitions en ligne.">
    <title>PétiSign</title>
    <link rel="shortcut icon" href="/Resources/img/logo/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Sources/css/error.css">
    <link rel="stylesheet" href="/Sources/css/style.css">
</head>
<body>
    <nav>
        <img src="/Resources/img/logo/logocompletsf.png" alt="Logo PétiSign" class="navlogo" onclick="location.href='/Sources/index.php'">
        <div class="links">
            <a href="/Sources/discover.php" class="navcontent">Découvrir PétiSign</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="/Sources/my_signatures.php" class="navcontent">Mes Signatures</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="/Sources/my_petitions.php" class="navcontent">Mes Pétitions</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a class="navcontent pfp_img" href="/Sources/login.php"><img class="pfp" src="/Resources/img/ui_icons/unlogged_user.png" alt=""></a>
            <!--<a id="navcontent" href="#user_list">UserMenu</a>-->
            <a class="mobile_menu" id="excep" href=""><img class="mobile_menu" src="/Resources/img/ui_icons/menu.png" alt=""></a>
        </div>
    </nav>


<div class="container">
    <div class="left">
        <img src="/Resources/img/ui_icons/erreur.png" alt="Erreur">
    </div>
    <div class="right">
        <h1>Erreur <?=$insertVal?> :</h1>
        <h2><?=$error_details?></h2>
    </div>
</div>
<hr id="error_hr">
<div class="buttons">
<button class="custom-button" id="back_to_menu" onclick="location.href='/Sources'" >Retourner à l'accueil</button>
</div>


<?php
include_once 'footer.php';
?>
