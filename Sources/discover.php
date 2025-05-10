<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    $user = 'Anonyme';
}else{
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $_SESSION['mail']);
    $stmt->execute();
    $user = $stmt->fetchColumn();
}

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'D1SC0V', $user, $user_ip, 'Visite de la page "Découvrir"');


?>

<link rel="stylesheet" href="css/searchbar.css">
<link rel="stylesheet" href="css/discover.css">

<div class="search-wrapper">
    <form method="get" action="search.php" class="search">
        <div class="search-container">
            <input type="text" id="query" name="query" placeholder=" ">
            <label for="query">Rechercher ...</label>
            <div class="separator"></div>
            <button type="submit">
                <img src="../Resources/img/ui_icons/loupe.png" alt="Search">
            </button>
        </div>
    </form>
</div>

<hr id="after_search">

<?php
$number_of_cards = 5;
?>

<div class="trending">
    <div class="textheader">
        <h2 class="highlighted-text trendinghigh">En ce moment ...</h2>
        <p>Découvrez les dernières pétitions</p>
    </div>
    <div class="scrollable">
    <?php

    

    $five_most_recent_petitions_stmt = $pdo->prepare("SELECT * FROM PETITION ORDER BY date DESC LIMIT :limit");
    $five_most_recent_petitions_stmt->bindValue(':limit', $number_of_cards, PDO::PARAM_INT);
    $five_most_recent_petitions_stmt->execute();
    $five_most_recent_petitions = $five_most_recent_petitions_stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach($five_most_recent_petitions as $petition) {
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

        <div class="card see_more">
            <a class="see_more_link"  href="search.php"><img src="../Resources/img/ui_icons/greater.png" alt="See More"></a>
        </div>
    </div>
</div>

<?php

$get_all_categories_stmt = $pdo->prepare("SELECT id, name FROM CATEGORY");
$get_all_categories_stmt->execute();
$categories = $get_all_categories_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($categories as $category) {
    $category_id = $category['id'];

    $number_of_pet_in_category_stmt = $pdo->prepare("SELECT COUNT(*) FROM PETITION WHERE category = :category");
    $number_of_pet_in_category_stmt->bindParam(':category', $category_id);
    $number_of_pet_in_category_stmt->execute();
    $number_of_pet_in_category = $number_of_pet_in_category_stmt->fetchColumn();

    if ($number_of_pet_in_category == 0) {
        echo '
        <div class="trending spacing" id="first_after_title">
            <div class="textheader categories_header">
                <div class="header_left">
                    <h2 class="highlighted-text trendinghigh">' . $category['name'] . '</h2>
                </div>
            </div>
        </div>
        <div class="empty_category">
            <img src="../Resources/img/ui_icons/empty.png" class="small" alt="Empty Category">
            <p>Il n\'y a pas de pétition dans cette catégorie pour le moment.</p>
        </div>';
    } else {
        echo '
        <div class="trending spacing" id="first_after_title">
            <div class="textheader categories_header">
                <div class="header_left">
                    <h2 class="highlighted-text trendinghigh">' . $category['name'] . '</h2>
                </div>';

        if ($number_of_pet_in_category >= 5) {
            echo '
                <div class="header_right">
                    <a href="">Tout Afficher</a>
                </div>';
        }

        echo '
            </div>
            <div class="scrollable">';

        $petitions_stmt = $pdo->prepare("SELECT * FROM PETITION WHERE category = :category LIMIT :limit");
        $petitions_stmt->bindParam(':category', $category_id, PDO::PARAM_INT);
        $petitions_stmt->bindValue(':limit', $number_of_cards, PDO::PARAM_INT);
        $petitions_stmt->execute();
        $petitions = $petitions_stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($petitions as $petition) {
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

        echo '
            </div>
        </div>';
    }
}

?>

<?php
include_once 'footer.php';
?>