<?php

session_start();
if(!isset($_SESSION['mail'])){
    header("Location: login.php");
    exit();
}

include_once 'header.php';
include_once 'database/database.php';

$user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$user_id_stmt->execute();
$user_id = $user_id_stmt->fetchColumn();

$petition_creator = $pdo->prepare("SELECT user FROM PETITION WHERE id = :id");
$petition_creator->bindParam(':id', $_GET['id']);
$petition_creator->execute();
$petition_creator = $petition_creator->fetchColumn();

if($petition_creator != $user_id){
    header("Location: my_petitions.php");
    exit();
}

$petition_info = $pdo->prepare("SELECT title, description, signature_goal FROM PETITION WHERE id = :id");
$petition_info->bindParam(':id', $_GET['id']);
$petition_info->execute();
$petition_info = $petition_info->fetch(PDO::FETCH_ASSOC);

if($petition_info){
    $petition_title = $petition_info['title'];
    $petition_description = $petition_info['description'];
    $petition_signature_goal = $petition_info['signature_goal'];
} else {
    header("Location: my_petitions.php");
    exit();
}

?>

<link rel="stylesheet" href="css/create_petition.css">

<div class="page_container">
    <h1 class="highlighted-text">Modifier ma Pétition</h1>
    <hr>
    <form method="post" action="Processus/update_petition.php">
        
        <div class="entries">
            <div class="entries">
                <input name="name" id="name" type="text" required placeholder=" " value="<?php echo $petition_title ?>" maxlength=60 onkeyup="count('name_counter',this,60)">
                <label for="name">Nom de la Pétition</label>
            </div>
            <div class="limit positioned" id="name_counter" >
                <p>Limite de caractères : 0 / 60</p>
            </div>
            <div class="space"></div>
            <div class="entries">
                <div class="area">
                    <textarea required name="description" id="description" maxlength=800 onkeyup="count('desc_counter',this,800)"><?php echo $petition_description ?></textarea>
                    <label for="description" class="textarea_label">Description de la Pétition</label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 800</p>
                </div>
            </div>
            <div class="entries">
                <input name="objectif" id="objectif" type="number" value=<?php echo $petition_signature_goal ?> min="10" required placeholder=" ">
                <label for="objectif">Objectif de Signatures</label>
            </div>
            <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
            <hr class="form_hr">
        </div>
        <button type="button" class="custom-button cancel" onclick="window.location.href='my_petitions.php';">Annuler</button>
        <button type="submit" class="custom-button validate">Valider</button>
    </form>
</div>

<script>

  document.addEventListener('DOMContentLoaded', function() {
    count('name_counter', document.getElementById('name'), 60);
    count('desc_counter', document.getElementById('description'), 800);
  });

</script>

<script src="js/count_characters.js"></script>

<?php
include_once 'footer.php';
?>
