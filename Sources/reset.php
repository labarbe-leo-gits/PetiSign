<?php

session_start();

if(isset($_SESSION['mail'])){
    header('Location: index.php');
    exit();
}

include_once 'header.php';
include_once 'database/database.php';
include_once 'checker.php';

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Changement de mot de passe</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="forgot_pswd.php?send_code=1">
        <div class="entries">
            <div class="entries">
                <p>Afin de confirmer votre identité, veuillez entrer l'adresse e-mail associée à votre compte afin de recevoir un code de vérification</p>
            </div>
            <div class="entries">
                <input name="new" id="new" type="email" required placeholder=" ">
                <label for="new">Adresse e-mail</label>
            </div>
        </div>
        <button class="custom-button loginbtn" type="submit">Continuer</button>
    </form>
</div>

<?php
include_once 'footer.php'
?>