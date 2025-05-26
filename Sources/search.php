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
    $insertVal = trim($_GET['query']);
} else {
    $insertVal = "";
}

$stmt = $pdo->prepare("SELECT * FROM CATEGORY ORDER BY name ASC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_GET['category_id'])) {
    $selectedCategory = $_GET['category_id'];
} else {
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';
}
?>

<div class="search-wrapper">
    <form method="get" action="search.php" class="search" id="searchForm">
        <div class="search-container">
            <input value="<?= htmlspecialchars($insertVal, ENT_QUOTES, 'UTF-8') ?>" type="text" id="query" name="query" placeholder=" ">
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
    $sql = "SELECT * FROM PETITION";
    $params = [];
    
    if($selectedCategory != 'all') {
        $sql .= " WHERE category = :category";
        $params[':category'] = $selectedCategory;
        
        if(!empty($insertVal)) {
            $sql .= " AND (title LIKE :query OR description LIKE :query)";
            $params[':query'] = '%' . $insertVal . '%';
        }
    } else if(!empty($insertVal)) {
        $sql .= " WHERE title LIKE :query OR description LIKE :query";
        $params[':query'] = '%' . $insertVal . '%';
    }
    
    $get_all_petitions = $pdo->prepare($sql);
    
    foreach($params as $key => $value) {
        $get_all_petitions->bindValue($key, $value);
    }
    
    $get_all_petitions->execute();
    $all_petitions = $get_all_petitions->fetchAll(PDO::FETCH_ASSOC);

    if(count($all_petitions) > 0) {
        foreach($all_petitions as $petition) {
            echo '
            <div class="card">
                <div class="cardheader">
                    <img src="../Resources/img/petition_selection/' . htmlspecialchars($petition['image_id']) . '.jpg" alt="">
                </div>
                <div class="cardcontent">
                    <div class="left">
                        <h3>' . htmlspecialchars($petition['title']) . '</h3>
                    </div>
                    <div class="right">
                        <a href="view_petition.php?id=' . htmlspecialchars($petition['id']) . '">Découvrir</a>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo '<p class="no-results">Aucune pétition trouvée</p>';
    }
    ?>
</div>

<script src="js/search_api.js"></script>

<?php
include_once 'footer.php';
?>