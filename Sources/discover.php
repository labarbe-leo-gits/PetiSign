<?php
include_once 'header.php';
include_once 'database/database.php';
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
    for ($i = 0; $i < $number_of_cards; $i++) {
        print '
        <div class="card">
            <div class="cardheader">
                <img src="../Resources/img/bg/protest.jpg" alt="">
            </div>
            <div class="cardcontent">
                <div class="left">
                    <h3>Titre</h3>
                </div>
                <div class="right">
                    <a href="">Découvrir</a>
                </div>
            </div>
        </div>';
    }
?>

        <div class="card see_more">
            <a class="see_more_link"  href=""><img src="../Resources/img/ui_icons/greater.png" alt="See More"></a>
        </div>
    </div>
</div>

<?php

$get_all_categories_stmt = $pdo->prepare("SELECT name FROM CATEGORY");
$get_all_categories_stmt->execute();
$categories = $get_all_categories_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($categories as $category){

    $get_category_id = $pdo->prepare("SELECT id FROM CATEGORY WHERE name = :name");
    $get_category_id->bindParam(':name', $category['name']);
    $get_category_id->execute();
    $category_id = $get_category_id->fetchColumn();

    $number_of_pet_in_category_stmt = $pdo->prepare("SELECT COUNT(*) FROM PETITION WHERE category = :category");
    $number_of_pet_in_category_stmt->bindParam(':category', $category_id);
    $number_of_pet_in_category_stmt->execute();
    $number_of_pet_in_category = $number_of_pet_in_category_stmt->fetchColumn();

    if($number_of_pet_in_category == 0){
        echo '
        <div class="trending spacing" id="first_after_title">
            <div class="textheader categories_header">
                <div class="header_left">
                    <h2 class="highlighted-text trendinghigh">'.$category['name'].'</h2>
                </div>
            </div>
        </div>
        <p>
            Il n\'y a pas de pétition dans cette catégorie pour le moment.
        </p>';
    }
    else{


        echo '
            <div class="trending spacing" id="first_after_title">
            <div class="textheader categories_header">
                <div class="header_left">
                    <h2 class="highlighted-text trendinghigh">'.$category['name'].'</h2>
                </div>
                <div class="header_right">
                    <a href="">Tout Afficher</a>
                </div>
            </div>
            <div class="scrollable">';

            if($number_of_pet_in_category < $number_of_cards){
                for($i = 0; $i < $number_of_cards; $i++){
                    echo '
                        <div class="card">
                            <div class="cardheader">
                                <img src="../Resources/img/bg/protest.jpg" alt="">
                            </div>
                            <div class="cardcontent">
                                <div class="left">
                                    <h3>Titre</h3>
                                </div>
                                <div class="right">
                                    <a href="">Découvrir</a>
                                </div>
                            </div>
                        </div>';
                }

                echo '<div class="card see_more">
                    <a class="see_more_link"  href=""><img src="../Resources/img/ui_icons/greater.png" alt="See More"></a>
                </div>
            </div>
        </div>
        ';
            }else{
            
                for($i = 0; $i < $number_of_cards; $i++){
                    echo '
                        <div class="card">
                            <div class="cardheader">
                                <img src="../Resources/img/bg/protest.jpg" alt="">
                            </div>
                            <div class="cardcontent">
                                <div class="left">
                                    <h3>Titre</h3>
                                </div>
                                <div class="right">
                                    <a href="">Découvrir</a>
                                </div>
                            </div>
                        </div>';
                }

                echo '<div class="card see_more">
                    <a class="see_more_link"  href=""><img src="../Resources/img/ui_icons/greater.png" alt="See More"></a>
                </div>
            </div>
        </div>
        ';}
    }
}

?>

<?php
include_once 'footer.php';
?>
