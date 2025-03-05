<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/profile.css">

<script src="js/profile.js" defer></script>

<div class="profile-container">
    <div class="profil_gauche">
        <h2 id="loginhigh" class="highlighted-text">Aperçu</h2>
        <div class="space"></div>
            <img src="../Resources/img/ui_icons/unlogged_user.png" alt="Avatar">
        <h2 id="nomdp">Nom d'utilisateur</h2>
        <p id="description_profile">Vous n'avez pas de description. Ajoutez en une puis enregistrer</p>
    </div>
    <!-- utile ? -->
    <div class="login_form" id="register_form">
        <h1 id="loginhigh" class="highlighted-text">Mes informations</h1>
        <hr id="loginhr">

        <form method="post" class="login" action="profile.php">
            <div class="entries">
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="mail" id="mail" type="email" placeholder=" " required> <!-- MODIFIER -->
                    <label for="mail">Adresse mail</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="username" id="username" type="text" placeholder=" " required> <!-- MODIFIER -->
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
                    <input class="editable" disabled name="anniv" id="anniv" type="date" required> <!-- MODIFIER -->
                    <label for="anniv">Date de naissance</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <textarea class="editable" disabled name="description" id="description"></textarea>
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