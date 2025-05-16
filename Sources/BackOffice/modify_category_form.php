<?php
include_once 'header.php';
include_once '../../database/database.php';
include_once '../checker.php';

$id = $_GET['id'];

$nameStmt = $pdo->prepare("SELECT name FROM CATEGORY WHERE id = :id");
$nameStmt->bindParam(':id', $id, PDO::PARAM_INT);
$nameStmt->execute();
$name = $nameStmt->fetchColumn();

if ($name === false) {
    echo "Error: Unable to fetch data for ID $id";
    exit();
}
?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Modification de la Catégorie</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/modify_category.php">
            <div class="entries">
                <div class="entries">
                    <input name="name" id="name" type="text" required value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>">
                    <label for="name">Nom</label>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
            <button class="custom-button" id="add_btn" type="submit">Modifier</button>
        </form>
        <button class="custom-button" onclick="window.location.href='database_gestion.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>