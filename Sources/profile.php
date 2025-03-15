<?php
include_once 'header.php';
include_once 'database/database.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

try{
    $get_user = $pdo->prepare('SELECT username FROM USER WHERE email = :mail');
    $get_user->bindParam(':mail', $mail);
    $get_user->execute();
    $user = $get_user->fetchColumn();

    $get_description = $pdo->prepare('SELECT description FROM USER WHERE email = :mail');
    $get_description->bindParam(':mail', $mail);
    $get_description->execute();
    $description = $get_description->fetchColumn();

    $get_birthdate = $pdo->prepare('SELECT birthdate FROM USER WHERE email = :mail');
    $get_birthdate->bindParam(':mail', $mail);
    $get_birthdate->execute();
    $birthdate = $get_birthdate->fetchColumn();

}catch (PDOException $e){
    echo 'Erreur : '.$e->getMessage();
}

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/profile.css">

<script src="js/profile.js" defer></script>

<div class="profile-container">
    <div class="profil_gauche">
        <h2 id="loginhigh" class="highlighted-text">Aperçu</h2>
        <div class="space"></div>
            <img src="../Resources/img/ui_icons/unlogged_user.png" alt="Avatar">
        <h2 id="nomdp"><?=$user?></h2>
        <p id="description_profile"><?=$description?></p>
    </div>
    <!-- utile ? -->
    <div class="login_form" id="register_form">
        <h1 id="loginhigh" class="highlighted-text">Mes informations</h1>
        <hr id="loginhr">

        <form method="post" class="login" action="profile.php">
            <div class="entries">
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="mail" id="mail" type="email" placeholder=" " value="<?= htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') ?>" required> <!-- MODIFIER -->
                    <label for="mail">Adresse mail</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="username" id="username" type="text" placeholder=" " value="<?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?>" required> <!-- MODIFIER -->
                    <label for="username">Nom d'utilisateur</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <select class="editable" disabled name="gender" id="gender" onchange="updateLabel(this)">
                        <option value="homme">Homme</option>
                        <option value="femme">Femme</option>
                        <option value="autre">Autre</option>
                    </select>
                    <label for="gender" class="labels">Genre</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="password" id="password" type="password" placeholder=" " required> <!-- MODIFIER -->
                    <label for="password">Mot de passe</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="anniv" id="anniv" type="date" value="<?= htmlspecialchars($birthdate, ENT_QUOTES, 'UTF-8') ?>" required> <!-- MODIFIER -->
                    <label for="anniv">Date de naissance</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <textarea class="editable" disabled name="description" id="description"><?=$description?></textarea>
                    <label for="description">Description</label>
                </div>
            </div>

            <button type="button" id="loginbtn" class="custom-button">Modifier</button>
        </form>
    </div>
</div>

<?php
include_once 'footer.php';
?>