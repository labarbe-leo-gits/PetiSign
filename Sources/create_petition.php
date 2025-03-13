<?php
include_once 'header.php';
include_once 'database/database.php'
?>

<link rel="stylesheet" href="css/create_petition.css">

<div class="page_container">
    <h1 class="highlighted-text">Nouvelle Pétition</h1>
    <hr>
    <form method="post" action="create_petition_process.php">
        <p class="category_text">Catégorie</p>
        <select name="category" id="category" default="Catégorie" required>
            <option value=""></option>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM CATEGORY");
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
        </div>
        <button type="submit" class="custom-button validate">Valider</button>
    </form>
</div>

<script src="js/count_characters.js"></script>

<?php
include_once 'footer.php';
?>
