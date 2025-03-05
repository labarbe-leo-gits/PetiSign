<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FED78B">
    <meta name="description" content="PétiSign est une plateforme de pétitions en ligne.">
    <title>PétiSign</title>
    <link rel="shortcut icon" href="../Resources/img/logo/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dark.css">
    <link rel="stylesheet" href="css/mobile_menu.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <script src="js/menu.js"></script>
    <nav>
        <img src="../Resources/img/logo/logocompletsf.png" alt="Logo PétiSign" class="navlogo" onclick="location.href='index.php'">
        <p class="dot navcontent">&#x25CF;</p>
        <button id="dark-mode-toggle">
            <svg class="cs-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 480 480" xml:space="preserve"><path d="M459.782 347.328c-4.288-5.28-11.488-7.232-17.824-4.96-17.76 6.368-37.024 9.632-57.312 9.632-97.056 0-176-78.976-176-176 0-58.4 28.832-112.768 77.12-145.472 5.472-3.712 8.096-10.4 6.624-16.832S285.638 2.4 279.078 1.44C271.59.352 264.134 0 256.646 0c-132.352 0-240 107.648-240 240s107.648 240 240 240c84 0 160.416-42.688 204.352-114.176 3.552-5.792 3.04-13.184-1.216-18.496z"/></svg>
            <img class="cs-sun" aria-hidden="true" src="https://csimg.nyc3.digitaloceanspaces.com/Contact-Page/sun.svg" decoding="async" alt="sun" width="15" height="15">
        </button>
        <div class="links">
            <a href="discover.php" class="navcontent">Découvrir PétiSign</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="my_signatures.php" class="navcontent">Mes Signatures</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="my_petitions.php" class="navcontent">Mes Pétitions</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a class="navcontent pfp_img" href="login.php"><img class="pfp" src="../Resources/img/ui_icons/unlogged_user.png" alt=""></a>
            <!--<a id="navcontent" href="#user_list">UserMenu</a>-->
            <a class="men" id="excep" href="javascript:show_popup()"><img class="mobile_menu" src="../Resources/img/ui_icons/menu.png" alt=""></a>
        </div>
    </nav>
    <div class="menu_container">
        <div class="mobile_menu_popup">
            <h2 class="highlighted-text" id="navhigh">Menu de Navigation</h2>
            <hr id="menu_separator">
            <div class="menu_item"><a href="discover.php">Découvrir PétiSign</a></div>
            <div class="menu_item"><a href="my_signatures.php">Mes Signatures</a></div>
            <div class="menu_item"><a href="my_petitions.php">Mes Pétitions</a></div>
            <div class="menu_item"><a href="login.php">Mon Compte</a></div>
        </div>
    </div>
