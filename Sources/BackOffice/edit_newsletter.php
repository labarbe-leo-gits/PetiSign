<?php
include_once 'header.php';
include_once '../database/database.php';
include_once '../checker.php';

$newsletterId = $_GET['id'] ?? null;
$filteredId = filter_var($newsletterId, FILTER_SANITIZE_NUMBER_INT);

if ($filteredId === null || $filteredId === false) {
    header('Location: newsletter.php');
    exit;
}

$all_infos = $pdo->prepare('SELECT * FROM NEWSLETTER WHERE id = :id');
$all_infos->bindParam(':id', $filteredId, PDO::PARAM_INT);
$all_infos->execute();
$newsletter = $all_infos->fetch(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_createnews.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modification de la Newsletter</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/update_newsletter.php">
            <div class="entries">
                <div class="entries">
                    <input name="title" id="title" type="text" value="<?=$newsletter['title']?>" required placeholder=" " maxlength="60">
                    <label for="title">Titre</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                <div class="area">
                <textarea required name="message" id="message" maxlength="1200"><?=str_replace('<br />', "\n", $newsletter['content'])?></textarea>
                    <label for="message" class="textarea_label">Message</label>
                </div>
                </div>
                <input type="hidden" name="id" value="<?=$newsletter['id']?>">
            </div>
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='newsletter.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php'
?>