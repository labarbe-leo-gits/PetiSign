<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_upload.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Upload</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/captcha.php">
            <div class="entries">
                <div class="entries">
                    <div class="readonly-field"><a id="user" href="/Sources/view_profile.php?id=<?php echo $id ?>"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></a></div>
                    <label for="">cc</label>
                </div>
                <div class="space"></div>
            </div>
            <button class="custom-button" id="add_btn" type="submit">Upload l'image</button>
        </form>
        <button class="custom-button" onclick="window.location.href='captcha.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>