<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/register.css">

<div class="login_form" id="register_form">
    <h1 id="loginhigh" class="highlighted-text">Inscription</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="register.php"> <!-- à changer action -->
        <div class="entries">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" ">
                <label for="mail">Adresse e-mail</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="username" id="username" type="text" required placeholder=" ">
                <label for="username">Nom d'utilisateur</label>
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
                <p class="captcha_text">Quelle est la capitale de la Chine ?</p>
            </div>
            <div class="entries">
                <input name="answer" id="answer" type="text" required placeholder=" ">
                <label for="answer">Réponse</label>
            </div>
        </div>
        <button class="custom-button" id="loginbtn" type="submit">S'inscrire</button>
    </form>
    <hr id="loginhr2">
    <p class="smallTxt">Déjà membre ? <a href="login.php">Se connecter</a></p>
</div>

<?php
include_once 'footer.php'
?>