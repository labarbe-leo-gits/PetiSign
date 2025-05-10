<?php

session_start();
if(!isset($_SESSION['mail'])){
    header("Location: login.php");
    exit();
}

include_once 'header.php';
include_once 'database/database.php';
include_once 'checker.php';

$user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$user_id_stmt->execute();
$user_id = $user_id_stmt->fetchColumn();

?>

<link rel="stylesheet" href="css/create_petition.css">
<link rel="stylesheet" href="css/img_selector.css">

<div class="page_container">
    <h1 class="highlighted-text">Nouvelle Pétition</h1>
    <hr>
    <form method="post" action="Processus/create_petition.php">
        <p class="category_text">Catégorie</p>
        <select name="category" id="category" default="Catégorie" required>
            <option value=""></option>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM CATEGORY ORDER BY name ASC");
                $stmt->execute();
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($categories as $category){
                    echo "<option value=" . $category['id'] . ">" . $category['name'] . "</option>";
                }
            }
            catch(PDOException $e){
                echo "error";
            }
            ?>
        </select>
        <div class="entries">
            <div class="entries">
                <input name="name" id="name" type="text" required placeholder=" " maxlength=60 onkeyup="count('name_counter',this,60)">
                <label for="name">Nom de la Pétition</label>
            </div>
            <div class="limit positioned" id="name_counter" >
                <p>Limite de caractères : 0 / 60</p>
            </div>
            <div class="space"></div>
            <div class="entries">
                <div class="area">
                    <textarea required name="description" id="description" maxlength=800 onkeyup="count('desc_counter',this,800)"></textarea>
                    <label for="description" class="textarea_label">Description de la Pétition</label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 800</p>
                </div>
            </div>
            <div class="entries">
                <input name="objectif" id="objectif" type="number" min="10" required placeholder=" ">
                <label for="objectif">Objectif de Signatures</label>
            </div>
            <hr class="form_hr">
            <div class="entries">
                <button type="button" class="custom-button validate" id="showhide" onclick="show_popup()">Sélectionner une image</button>
                <input type="hidden" name="img_id" id="img_id" value="N/A">
                <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>">
            </div>
        </div>
        <hr class="form_hr">
        <button type="button" class="custom-button cancel" onclick="window.location.href='my_petitions.php';">Annuler</button>
        <button type="submit" class="custom-button validate">Valider</button>
    </form>
</div>

<div class="filter">&nbsp;</div>

<div class="container popup">
    <div class="img_container">
        <h2>Cliquez sur une image ci-dessous pour la sélectionner</h2>
        <hr id="top_hr">
        <?php
        $dir = '../Resources/img/petition_selection/';
        $files = scandir($dir);
        $files = array_filter($files, function($file) {
            return $file != '.' && $file != '..';
        });
        natsort($files);

        foreach ($files as $file) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);
            print "<button class='selectable img_btn' id='$fileName' value='$fileName'><img src='$dir$file' alt=''></button>";
        }
        ?>
        <hr id="bottom_hr">
        <div class="send"><button>Valider</button></div>
        <div class="img_cancel"><button onclick="hide_popup()">Annuler</button></div>
    </div>
</div>

<script src="js/count_characters.js"></script>
<script src="js/img_selector.js"></script>

<?php
include_once 'footer.php';
?>
