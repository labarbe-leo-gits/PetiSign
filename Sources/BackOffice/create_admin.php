<?php
include_once 'header.php';
include_once '../database/database.php';
include_once '../checker.php';

$get_all_benevoles_stmt = $pdo->prepare("SELECT id, username FROM USER WHERE is_benevole != 0");
$get_all_benevoles_stmt->execute();
$all_benevoles = $get_all_benevoles_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_addteam.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Nouveau Compte Administrateur</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/new_admin.php">
            <div class="entries">
                <div class="entries">
                    <input name="mail" id="mail" type="mail" required placeholder=" " required>
                    <label for="mail">Adresse e-mail</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="username" id="username" type="text" placeholder=" " required>
                    <label for="username">Nom d'utilisateur</label>
                </div>             
                <div class="space"></div>
                <div class="entries">
                    <input name="password" id="password" type="password" placeholder=" " required>
                    <label for="password">Mot de passe</label>
                </div>             
            </div>
            <button class="custom-button" id="add_btn" type="submit">Ajouter</button>
        </form>
        <button class="custom-button" onclick="window.location.href='users.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<script src="../js/team_creator.js"></script>

<?php
include_once 'footer.php';
?>