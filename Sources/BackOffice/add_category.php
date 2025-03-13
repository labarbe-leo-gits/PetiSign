<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Ajout d'une Catégorie</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/category.php">
            <div class="entries">
                <div class="entries">
                    <input name="name" id="name" type="text" required placeholder=" ">
                    <label for="name">Nom</label>
                </div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Ajouter</button>
        </form>
        <button class="custom-button" onclick="window.location.href='database_gestion.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>