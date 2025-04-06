<?php

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

include_once 'database/database.php';

$user_username = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
$user_username->bindParam(':mail', $_SESSION['mail']);
$user_username->execute();
$username = $user_username->fetchColumn();

$check_if_ban_exist_stmt = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = (SELECT id FROM USER WHERE email = :mail)");
$check_if_ban_exist_stmt->bindParam(':mail', $_SESSION['mail']);
$check_if_ban_exist_stmt->execute();
$check_if_ban_exist = $check_if_ban_exist_stmt->fetchColumn();

if($check_if_ban_exist == 0){
    header('Location: profile.php');
    exit();
}

$user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$user_id_stmt->execute();
$user_id = $user_id_stmt->fetchColumn();

$get_ban = $pdo->prepare("SELECT * FROM BAN WHERE id_user = :id");
$get_ban->bindParam(':id', $user_id);
$get_ban->execute();
$ban = $get_ban->fetch(PDO::FETCH_ASSOC);

$admin_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$admin_username->bindParam(':id', $ban['id_admin']);
$admin_username->execute();
$admin = $admin_username->fetchColumn();

$ban_reason_stmt = $pdo->prepare("SELECT reason FROM BAN WHERE id_user = :id");
$ban_reason_stmt->bindParam(':id', $user_id);
$ban_reason_stmt->execute();
$ban_reason = $ban_reason_stmt->fetchColumn();
$ban_reason = nl2br($ban_reason);

$expiration_stmt = $pdo->prepare("SELECT expiration FROM BAN WHERE id_user = :id");
$expiration_stmt->bindParam(':id', $user_id);
$expiration_stmt->execute();
$expiration = $expiration_stmt->fetchColumn();

$formated_date = date('d/m/Y', strtotime($expiration));
$expiration = $formated_date;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FED78B">
    <meta name="description" content="PétiSign est une plateforme de pétitions en ligne.">
    <title>Compte Banni</title>
    <link rel="shortcut icon" href="../Resources/img/logo/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dark.css">
    <link rel="stylesheet" href="css/mobile_menu.css">
    <link rel="stylesheet" href="css/ban.css">
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
            <?php
            if($is_admin != null){
                echo '<a href="BackOffice" class="navcontent
                ">Back Office</a>';
                echo '<p class="dot navcontent">&#x25CF;</p>';
            }
            ?>
            <a href="discover.php" class="navcontent">Découvrir PétiSign</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="<?php echo isset($_SESSION['mail']) ? 'my_signatures.php' : 'login.php'; ?>" class="navcontent">Mes Signatures</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a href="<?php echo isset($_SESSION['mail']) ? 'my_petitions.php' : 'login.php'; ?>" class="navcontent">Mes Pétitions</a>
            <p class="dot navcontent">&#x25CF;</p>
            <a class="navcontent pfp_img" href="<?php echo isset($_SESSION['mail']) ? 'profile.php' : 'login.php'; ?>"><img class="pfp" src="../Resources/img/ui_icons/unlogged_user.png" alt=""></a>
            <!--<a id="navcontent" href="#user_list">UserMenu</a>-->
            <a class="men" id="excep" href="javascript:show_popup()"><img class="mobile_menu" src="../Resources/img/ui_icons/menu.png" alt=""></a>
        </div>
    </nav>
    <div class="menu_container">
        <div class="mobile_menu_popup">
            <h2 class="highlighted-text" id="navhigh">Menu de Navigation</h2>
            <hr id="menu_separator">
            <?php
            if($is_admin != null){
                echo '<div class="menu_item"><a href="BackOffice">Back Office</a></div>';
            }
            ?>
            <div class="menu_item"><a href="discover.php">Découvrir PétiSign</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? 'my_signatures.php' : 'login.php'; ?>">Mes Signatures</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? 'my_petitions.php' : 'login.php'; ?>">Mes Pétitions</a></div>
            <div class="menu_item"><a href="<?php echo isset($_SESSION['mail']) ? 'profile.php' : 'login.php'; ?>">Mon Compte</a></div>
        </div>
    </div>
<div class="ban_container">
    <h2>Bonjour <?= $username ?>,</h2>
    <p class="header_text" id="first">Nous sommes désolés de vous informer que votre compte a été banni.</p>
    <p class="header_text">Voici les détails de votre bannissement :</p>

    <div class="details">
        <div class="details_item">
            <h3>Administrateur à l'origine du bannissement :</h3>
            <p><?= $admin ?></p>
        </div>
        <div class="details_item">
            <h3>Raison du bannissement :</h3>
            <p><?= $ban_reason ?></p>
        </div>
        <div class="details_item">
            <h3>Date de fin du bannissement :</h3>
            <p><?= $expiration ?></p>
    </div>
</div>
    <p class="header_text" id="second">Nous vous prions de bien vouloir respecter les règles de la plateforme à l'avenir.</p>
    <p class="header_text">Si vous pensez qu'il s'agit d'une erreur, n'hésitez pas à nous contacter.</p>
    <p class="header_text">Nous vous remercions de votre compréhension.</p>
    <p class="header_text" id="signature_text">Cordialement,</p>
    <p class="header_text" id="last">L'équipe de PétiSign</p>

    <button type="button" class="custom-button loginbtn" onclick="window.location.href='logout.php';">Déconnexion</button>
</div>

</div>

<link rel="stylesheet" href="css/backoffice_ban_user.css">

<div class="ban_container">
    <h2>Contactez-nous</h2>
    <form action="">
        <div class="container">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" " value="<?=$_SESSION['mail']?>" class="form-input">
                <label for="mail">Adresse e-mail</label>
            </div>
            <div class="entries">
                <input name="obj" id="obj" type="text" required placeholder=" " class="form-input">
                <label for="obj">Objet</label>
            </div>
            <div class="area">
            <p class="role_selector_text">Message</p>
                <textarea required name="message" id="message" maxlength="1200" placeholder="Écrivez votre message ici ..."></textarea>    
            </div>
            <button type="submit" class="custom-button loginbtn">Envoyer</button>
        </div>
    </form>
</div>

<?php
include_once 'footer.php';
?>