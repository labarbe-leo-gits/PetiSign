<?php
include_once 'header.php';
include_once '../../database/database.php';

$id = $_GET['id'];

$questionStmt = $pdo->prepare("SELECT question FROM CAPTCHA WHERE id = :id");
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

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modification du Captcha</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/modify_captcha.php">
            <div class="entries">
                <div class="entries">
                    <input name="question" id="question" type="text" required value="<?= htmlspecialchars($question, ENT_QUOTES, 'UTF-8') ?>" maxlength="255">
                    <label for="question">Question</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="answer" id="answer" type="text" required value="<?= htmlspecialchars($answer, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="answer">Réponse</label>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='captcha.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>