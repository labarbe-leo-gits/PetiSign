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

    $get_gender = $pdo->prepare('SELECT gender FROM USER WHERE email = :mail');
    $get_gender->bindParam(':mail', $mail);
    $get_gender->execute();
    $gender = $get_gender->fetchColumn();

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

        <form method="post" class="login" id="profile_form" action="Processus/profile_updater.php">
            <div class="entries">
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="mail" id="mail" type="email" placeholder=" " value="<?= htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="mail">Adresse mail</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="username" id="username" type="text" placeholder=" " value="<?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="username">Nom d'utilisateur</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <select class="editable" disabled name="gender" id="gender" onchange="updateLabel(this)">
                        <option value="Homme" <?php if ($gender == "Homme") echo 'selected'; ?>>Homme</option>
                        <option value="Femme" <?php if ($gender == "Femme") echo 'selected'; ?>>Femme</option>
                        <option value="Autre" <?php if ($gender == "Autre") echo 'selected'; ?>>Autre</option>
                        <option value="Non Renseigné" <?php if ($gender == "Non Renseigné") echo 'selected'; ?>>Non Renseigné</option>
                    </select>
                    <label for="gender" class="labels">Genre</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="anniv" id="anniv" type="date" value="<?= htmlspecialchars($birthdate, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="anniv">Date de naissance</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <textarea class="editable" disabled name="description" id="description"><?=$description?></textarea>
                    <label for="description">Description</label>
                </div>
            </div>
            
            <button type="button" onclick="window.location.href='password_form.php'" class="custom-button loginbtn">Changer mon mot de passe</button>
            <button type="button" id="loginbtn" class="custom-button loginbtn">Modifier</button>
            <button type="submit" id="save_btn" class="custom-button loginbtn">Enregistrer</button>
        </form>
        <hr id="btn_hr">
        <button type="button" class="custom-button loginbtn" onclick="window.location.href='logout.php';">Déconnexion</button>
    </div>
</div>

<?php
include_once 'footer.php';
?>