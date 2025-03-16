<?php
include_once 'header.php';
include_once 'database/database.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

?>

<link rel="stylesheet" href="css/mysigns.css">

<div class="title">
    <h1 class="highlighted-text" id="mysigns">Mes Signatures</h1>
    <hr>
    <a class="filters" href=""> <img src="../Resources/img/ui_icons/filter.png" id="filter" alt="Filtres">  Filtres</a>
</div>

<?php
$card_num = 5
?>

<div class="pet_container">
    <?php

    for($i = 0; $i < $card_num; $i++){
        print '
        <div class="sample_pet">
            <div class="header">
                <img src="../Resources/img/bg/tigers.jpg" alt="Image de couverture pétition">
                <p class="category">Animaux</p>
            </div>
            <div class="content">
                <h2 class="title">Title</h2>
                <hr class="pet_sep">
                <p class="description">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut finibus  fermentum dictum. Pellentesque habitant morbi tristique senectus et  netus et malesuada fames ac turpis egestas. Phasellus scelerisque  viverra efficitur. Nam in lectus sodales, maximus purus ut, semper quam. Donec pulvinar ultrices sem, sit amet rutrum lacus pulvinar non.</p>
            </div>
            <div class="footer">
                <p class="sign">XXX / XXX Signatures</p>
                <p class="author">Author</p>
            </div>
            <div class="footer_link">
                <a href="" class="see_pet">Voir la Pétition</a>
            </div>
        </div>
        ';
    }

    ?>

</div>

<?php
include_once 'footer.php';
?>