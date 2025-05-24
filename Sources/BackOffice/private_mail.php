<?php
include_once 'header.php';
include_once '../checker.php';
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_createnews.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Nouveau Mail</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/send_private_mail.php">
            <div class="entries">
                <div class="entries">
                    <input name="title" id="title" type="text" required placeholder=" " maxlength="60">
                    <label for="title">Objet</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                <div class="area">
                    <textarea required name="message" id="message" maxlength="1200"></textarea>
                    <label for="message" class="textarea_label">Message</label>
                </div>
                <input type="hidden" name="id" id="id" value="<?php echo $_GET['user_id']; ?>">
                </div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Envoyer</button>
        </form>
        <button class="custom-button" onclick="window.location.href='users.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>