<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="css/create_petition.css">

<div class="page_container">
    <h1>Nouvelle Pétition</h1>
    <hr>
    <form method="post" action="create_petition_process.php">
        <select name="category" id="category" default="Catégorie" required>
            <option value="Catégorie">Catégorie</option>
        </select>
        <div class="entries">
            <div class="entries">
                <input name="name" id="name" type="text" required placeholder=" ">
                <label for="name">Nom de la Pétition</label>
            </div>
            <div class="limit">
                <p>Limite de caractères : XX / 60</p>
            </div>
            <div class="space"></div>
            <div class="entries">
                <div class="area">
                    <textarea required name="description" id="description"></textarea>
                    <label for="description" class="textarea_label">Description de la Pétition</label>
                </div>
                <div class="limit">
                    <p>Limite de caractères : XXX / 800</p>
                </div>
            </div>
            <div class="entries">
                <input name="objectif" id="objectif" type="number" min="10" required placeholder=" ">
                <label for="objectif">Objectif de Signatures</label>
            </div>
        </div>
        <button type="submit">Valider</button>
    </form>
</div>
<?php
include_once 'footer.php';
?>
