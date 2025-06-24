<?php
include_once 'header.php';
include_once 'database/database.php';

$stmt = $pdo->prepare("SELECT * FROM CAPTCHA WHERE state = 1 ORDER BY RAND() LIMIT 1");
$stmt->execute();
$captcha = $stmt->fetch();

$id = $captcha['id'];

if(isset($_GET['error'])){
    $json_file = file_get_contents('json/error_register.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['error'], $error_manager)){
        $insertVal = $_GET['error'];
    }
}

$error_details = $error_manager[$insertVal];

if(isset($_SESSION['mail'])){
    echo "<script>window.location.href = 'profile.php';</script>";
    exit();
}

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/register.css">

<?php

if(isset($_GET['error']) && isset($_GET['referer']) && ($_GET['referer'] == 'mail_verification' || $_GET['referer'] == 'register') && $_GET['error'] != '' && $_GET['referer'] != ''){
    echo '
    <div class="error">
        <div class="error_message">
            <p class="error_text">' . $error_details .'</p>
        </div>
    </div>
    ';
}
?>

<div class="login_form" id="register_form">
    <h1 id="loginhigh" class="highlighted-text">Inscription</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="mail_verification.php">
        <div class="entries">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" ">
                <label for="mail">Adresse e-mail</label>
                <div id="email-status" class="status-message"></div>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="username" id="username" type="text" required placeholder=" " maxlength=30>
                <label for="username">Nom d'utilisateur</label>
                <div id="username-status" class="status-message"></div>
            </div>
            <div class="space"></div>
            <div class="entries">
                    <input class="editable" name="anniv" id="anniv" type="date" required>
                    <label for="anniv">Date de naissance</label>
                </div>
            <hr id="register_separator">
            <div class="entries">
                <input name="password" id="password" type="password" required placeholder=" ">
                <label for="password">Mot de passe</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="confpassword" id="confpassword" type="password" required placeholder=" ">
                <label for="confpassword">Confirmer</label>
            </div>
            <hr id="register_separator_2">
            <div class="entries">
                <h2 id="captchahigh" class="highlighted-text">Captcha</h2>
            </div>
            <div class="entries">
                <?php
                $captcha['question'] = html_entity_decode($captcha['question'], ENT_QUOTES, 'UTF-8');
                ?>
                <p class="captcha_text"><?=$captcha['question'];?></p>
            </div>
            <div class="entries">
                <input name="answer" id="answer" type="text" required placeholder=" ">
                <label for="answer">Réponse</label>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <button class="custom-button loginbtn" type="submit" id="submit-btn">S'inscrire</button>
    </form>
    <hr id="loginhr2">
    <p class="smallTxt">Déjà membre ? <a href="login.php">Se connecter</a></p>
</div>

<script src="js/register_rt.js"></script>

<?php
include_once 'footer.php'
?>