<?php
include_once 'header.php';
include_once '../database/database.php';
include_once 'security.php';

$get_all_benevoles_stmt = $pdo->prepare("SELECT id, username FROM USER WHERE is_benevole != 0");
$get_all_benevoles_stmt->execute();
$all_benevoles = $get_all_benevoles_stmt->fetchAll(PDO::FETCH_ASSOC);

$current_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$current_user_id->bindParam(':mail', $_SESSION['mail']);
$current_user_id->execute();
$current_user_id = $current_user_id->fetchColumn();

?>

<link rel="stylesheet" href="../css/backoffice_addcaptcha.css">
<link rel="stylesheet" href="../css/backoffice_addteam.css">

<div class="right_panel">
    <div class="captcha_form secondform" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Nouvelle équipe</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/create_team.php">
            <div class="entries">
                <div class="entries">
                    <input name="name" id="name" type="text" required placeholder=" ">
                    <label for="name">Nom</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="sector" id="sector" type="text" placeholder=" ">
                    <label for="sector">Secteur</label>
                </div>
                <div class="space"></div>
                
                <h2>Membres de l'équipe</h2>
                <div class="entries benevoles_selector">
                    <?php foreach ($all_benevoles as $benevole): ?>
                        <?php if($benevole['id'] == $current_user_id){continue;} ?>
                        <div class="selectable" data-id="<?php echo $benevole['id']; ?>">
                            <?php echo htmlspecialchars($benevole['username'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="selected_benevoles" id="selected_benevoles" value="0">
                </div>               
            </div>
            <button class="custom-button" id="add_btn" type="submit">Ajouter</button>
        </form>
        <button class="custom-button" onclick="window.location.href='index.php'" id="cancel_btn">Annuler</button>
    </div>
</div>
</div>

<script src="../js/team_creator.js"></script>

<?php
include_once 'footer.php';
?>