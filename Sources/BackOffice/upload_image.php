<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_upload.css">
<link rel="stylesheet" href="../css/file_input.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Upload</h1>
        <hr id="loginhr">
        <div class="img_warn">
            <p class="warning">Formats d'image acceptés : JPG</p>
            <p class="warning">Taille maximale : 10 Mo</p>
        </div>
        <form action="/Resources/upload.php" method="post" enctype="multipart/form-data" class="login">
            <div class="entries">
            <div class="file-upload-container">
                <input type="file" id="fileToUpload" name="fileToUpload" class="file-input" required>
                <button id="fileButton" class="file-button" type="button">Sélectionnez un fichier</button>
            </div>
            <div class="space"></div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Upload l'image</button>
        </form>
        <button class="custom-button" onclick="window.location.href='database_gestion.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<script src="../js/file_input.js"></script>

<?php
include_once 'footer.php'
?>