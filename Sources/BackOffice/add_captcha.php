<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Ajout d'un Captcha Q&A</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/captcha.php">
            <div class="entries">
                <div class="entries">
                    <input name="question" id="question" type="text" required placeholder=" " maxlength="60">
                    <label for="question">Question</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="answer" id="answer" type="text" required placeholder=" ">
                    <label for="answer">Réponse</label>
                </div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Ajouter</button>
        </form>
        <button class="custom-button" onclick="window.location.href='captcha.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>