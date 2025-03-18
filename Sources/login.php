<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Connexion</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="Processus/login.php"> <!-- à changer action -->
        <div class="entries">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" ">
                <label for="mail">Adresse e-mail</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="password" id="password" type="password" required placeholder=" ">
                <label for="password">Mot de passe</label>
            </div>
        </div>
        <button class="custom-button loginbtn" type="submit">Se Connecter</button>
    </form>
    <hr id="loginhr2">
    <p class="smallTxt">Pas encore membre ? <a href="register.php">S'inscrire</a></p>
</div>

<?php
include_once 'footer.php'
?>