<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'checker.php';
?>

<link rel="stylesheet" href="css/discover.css">
<link rel="stylesheet" href="css/searchbar.css">
<link rel="stylesheet" href="css/result.css">


<?php
if(isset($_GET['query'])){
    $insertVal = $_GET['query'];
}else{
    $insertVal = "";
}

$stmt = $pdo->prepare("SELECT * FROM CATEGORY ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<div class="search-wrapper">
    <form method="get" action="search.php" class="search" id="searchForm">
        <div class="search-container">
            <input value="<?=$insertVal?>" type="text" id="query" name="query" placeholder=" ">
            <label for="query">Rechercher ...</label>
            <div class="separator"></div>
            <button type="submit">
                <img src="../Resources/img/ui_icons/loupe.png" alt="Search">
            </button>
        </div>
        
        <div class="category-select">
            <select name="category" id="category">
                <option value="all" <?= $selectedCategory == 'all' ? 'selected' : '' ?>>Toutes les catégories</option>
                <?php foreach($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= $selectedCategory == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<hr id="after_search">

<div class="card-container" id="results-container">
    <?php
    
    $get_all_petitions = $pdo->prepare("SELECT * FROM PETITION");
    $get_all_petitions->execute();
    $all_petitions = $get_all_petitions->fetchAll(PDO::FETCH_ASSOC);

    foreach($all_petitions as $petition) {
        echo '
        <div class="card">
            <div class="cardheader">
                <img src="../Resources/img/petition_selection/' . $petition['image_id'] . '.jpg" alt="">
            </div>
            <div class="cardcontent">
                <div class="left">
                    <h3>' . $petition['title'] . '</h3>
                </div>
                <div class="right">
                    <a href="view_petition.php?id=' . $petition['id'] . '">Découvrir</a>
                </div>
            </div>
        </div>';
    }
    ?>
</div>

<script src="js/search_api.js"></script>

<?php
include_once 'footer.php';
?>