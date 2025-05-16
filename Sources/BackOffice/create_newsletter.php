<?php
include_once 'header.php';
include_once '../checker.php';
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_createnews.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Nouvelle Newsletter</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/newsletter.php">
            <div class="entries">
                <div class="entries">
                    <input name="title" id="title" type="text" required placeholder=" " maxlength="60">
                    <label for="title">Titre</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                <div class="area">
                    <textarea required name="message" id="message" maxlength="1200"></textarea>
                    <label for="message" class="textarea_label">Message</label>
                </div>
                </div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Créer</button>
        </form>
        <button class="custom-button" onclick="window.location.href='newsletter.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>