<?php
include_once 'header.php';
include_once '../../database/database.php';
include_once '../checker.php';

$id = $_GET['id'];

$usernamestmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$usernamestmt->bindParam(':id', $id, PDO::PARAM_INT);
$usernamestmt->execute();
$username = $usernamestmt->fetchColumn();

$mailstmt = $pdo->prepare("SELECT email FROM USER WHERE id = :id");
$mailstmt->bindParam(':id', $id, PDO::PARAM_INT);
$mailstmt->execute();
$mail = $mailstmt->fetchColumn();

$is_adminstmt = $pdo->prepare("SELECT is_admin FROM USER WHERE id = :id");
$is_adminstmt->bindParam(':id', $id, PDO::PARAM_INT);
$is_adminstmt->execute();
$is_admin = $is_adminstmt->fetchColumn();

$is_benevolestmt = $pdo->prepare("SELECT is_benevole FROM USER WHERE id = :id");
$is_benevolestmt->bindParam(':id', $id, PDO::PARAM_INT);
$is_benevolestmt->execute();
$is_benevole = $is_benevolestmt->fetchColumn();

if ($username === false || $is_admin === false || $is_benevole === false) {
    echo "Error: Unable to fetch data for ID $id";
    exit();
}
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/role_selector.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modification du profil Utilisateur</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/modify_user.php">
            <div class="entries">
                <div class="entries">
                    <input name="username" id="username" type="text" required value="<?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
                    <label for="username">Nom d'utilisateur</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="emailaddress" id="emailaddress" type="email" required value="<?= htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="emailaddress">Adresse e-mail</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="new_pswd" id="new_pswd" type="password" placeholder="">
                    <label for="new_pswd">Nouveau mot de passe</label>
                    <p class="input_subtext">* Laissez vide pour ne rien changer</p>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Administrateur</p>
                    <select class="role_selector" name="administrator" id="administrator">
                        <option value="0" <?php if ($is_admin == 0) echo 'selected'; ?>>Non</option>
                        <option value="1" <?php if ($is_admin == 1) echo 'selected'; ?>>Oui</option>
                    </select>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Bénévole</p>
                    <select class="role_selector" name="benevole" id="benevole">
                        <option value="0" <?php if ($is_benevole == 0) echo 'selected'; ?>>Non</option>
                        <option value="1" <?php if ($is_benevole == 1) echo 'selected'; ?>>Oui</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='users.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>