<?php
include_once 'header.php';
include_once '../../database/database.php';

$id = $_GET['id'];

$questionStmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$questionStmt->bindParam(':id', $id, PDO::PARAM_INT);
$questionStmt->execute();
$question = $questionStmt->fetchColumn();

$answerStmt = $pdo->prepare("SELECT answer FROM CAPTCHA WHERE id = :id");
$answerStmt->bindParam(':id', $id, PDO::PARAM_INT);
$answerStmt->execute();
$answer = $answerStmt->fetchColumn();

if ($question === false || $answer === false) {
    echo "Error: Unable to fetch data for ID $id";
    exit();
}
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../../css/role_selector.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modification du profil Utilisateur</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/modify_user.php">
            <div class="entries">
                <div class="entries">
                    <input name="question" id="question" type="text" required value="<?= htmlspecialchars($question, ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
                    <label for="question">Nom d'utilisateur</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <select class="role_selector" name="administrator" id="administrator">
                        <option value="0" <?php if ($administrator == 0) echo 'selected'; ?>>Non</option>
                        <option value="1" <?php if ($administrator == 1) echo 'selected'; ?>>Oui</option>
                    </select>
                    <label>Administrateur</label>
                </div>
                <div class="entries">
                    <select class="role_selector" name="benevole" id="benevole">
                        <option value="0" <?php if ($administrator == 0) echo 'selected'; ?>>Non</option>
                        <option value="1" <?php if ($administrator == 1) echo 'selected'; ?>>Oui</option>
                    </select>
                    <label>Bénévole</label>
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