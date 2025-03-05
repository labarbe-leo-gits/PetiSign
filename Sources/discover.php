<?php
include_once 'header.php'
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

<div class="trending"> <!-- le container est scrollable. Il faudra automatiser les cards avec php mais c'est fonctionnel :) -->
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
                    <p>Description</p>
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

<div class="trending" id="first_after_title"> <!-- le container est scrollable. Il faudra automatiser les cards avec php mais c'est fonctionnel :) -->
    <div class="textheader categories_header">
        <div class="header_left">
            <h2 class="highlighted-text trendinghigh">Animaux</h2>
            <p>Pour nos amis les bêtes</p>
        </div>
        <div class="header_right">
            <a href="">Tout Afficher</a>
        </div>
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
                    <p>Description</p>
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
include_once 'footer.php';
?>
