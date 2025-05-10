<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Changement de mot de passe</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="Processus/change_pswd.php"> <!-- à changer action -->
        <div class="entries">
            <div class="entries">
                <input name="old" id="old" type="password" required placeholder=" ">
                <label for="old">Mot de passe actuel</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="new" id="new" type="password" required placeholder=" ">
                <label for="new">Nouveau mot de passe</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="new_conf" id="new_conf" type="password" required placeholder=" ">
                <label for="new_conf">Confirmer</label>
            </div>
        </div>
        <button class="custom-button loginbtn" type="submit">Changer le mot de passe</button>
    </form>
    <button class="custom-button loginbtn" onclick="window.location.href='profile.php'" type="button">Annuler</button>
</div>

<?php
include_once 'footer.php'
?>