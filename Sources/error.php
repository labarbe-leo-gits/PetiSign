<?php

session_start();

if(isset($_SESSION['mail'])){
    $mail = $_SESSION['mail'];
    
    

    $is_admin = $_SESSION['is_admin'];
    $is_benevole = $_SESSION['is_benevole'];

}
else{
    $mail = null;
    $is_admin = null;
    $is_benevole = null;
}

if(isset($_GET['code'])){
    $json_file = file_get_contents('json/error_manager.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['code'], $error_manager)){
        $insertVal = $_GET['code'];
    } else {
        $insertVal = " ";
    }
} else {
    header('location: /Sources/index.php');
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
    <link rel="stylesheet" href="/Sources/css/mobile_menu.css">
    <link rel="stylesheet" href="/Sources/css/dark.css">
</head>
<body>
<script src="/Sources/js/menu.js"></script>
    <nav>
        <img src="/Resources/img/logo/logocompletsf.png" alt="Logo PétiSign" class="navlogo" onclick="location.href='/Sources/index.php'">
        <p class="dot navcontent">&#x25CF;</p>
        <button id="dark-mode-toggle">
            <svg class="cs-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 480" xml:space="preserve"><path d="M459.782 347.328c-4.288-5.28-11.488-7.232-17.824-4.96-17.76 6.368-37.024 9.632-57.312 9.632-97.056 0-176-78.976-176-176 0-58.4 28.832-112.768 77.12-145.472 5.472-3.712 8.096-10.4 6.624-16.832S285.638 2.4 279.078 1.44C271.59.352 264.134 0 256.646 0c-132.352 0-240 107.648-240 240s107.648 240 240 240c84 0 160.416-42.688 204.352-114.176 3.552-5.792 3.04-13.184-1.216-18.496z"/></svg>
            <img class="cs-sun" aria-hidden="true" src="https://csimg.nyc3.digitaloceanspaces.com/Contact-Page/sun.svg" decoding="async" alt="sun" width="15" height="15">
        </button>
        <div class="links">
            <?php
            if($is_admin != null){
                echo '<a href="/Sources/BackOffice" class="navcontent
                ">Back Office</a>';
                echo '<p class="dot navcontent">&#x25CF;</p>';
            }
            ?>
            <a href="/Sources/discover.php" class="navcontent">Découvrir PétiSign</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="<?php echo isset($_SESSION['mail']) ? '/Sources/my_signatures.php' : '/Sources/login.php'; ?>" class="navcontent">Mes Signatures</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="<?php echo isset($_SESSION['mail']) ? '/Sources/my_petitions.php' : '/Sources/login.php'; ?>" class="navcontent">Mes Pétitions</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a class="navcontent pfp_img" href="<?php echo isset($_SESSION['mail']) ? '/Sources/profile.php' : '/Sources/login.php'; ?>"><img class="pfp" src="/Resources/img/ui_icons/unlogged_user.png" alt=""></a>
            <!--<a id="navcontent" href="#user_list">UserMenu</a>-->
            <a class="men" id="excep" href="javascript:show_popup()"><img class="mobile_menu" src="/Resources/img/ui_icons/menu.png" alt=""></a>
        </div>
    </nav>
    <div class="menu_container">
        <div class="mobile_menu_popup">
            <h2 class="highlighted-text" id="navhigh">Menu de Navigation</h2>
            <hr id="menu_separator">
            <?php
            if($is_admin != null){
                echo '<div class="menu_item"><a href="/Sources/BackOffice">Back Office</a></div>';
            }
            ?>
            <div class="menu_item"><a href="discover.php">Découvrir PétiSign</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? '/Sources/my_signatures.php' : '/Sources/login.php'; ?>">Mes Signatures</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? '/Sources/my_petitions.php' : '/Sources/login.php'; ?>">Mes Pétitions</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? '/Sources/profile.php' : '/Sources/login.php'; ?>">Mon Compte</a></div>
        </div>
    </div>


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
include_once '/Sources/footer.php';
?>
