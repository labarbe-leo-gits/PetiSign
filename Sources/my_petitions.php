<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="css/mypet.css">

<div class="title">
    <h1 class="highlighted-text" id="mysigns">Mes Pétitions</h1>
    <hr>
    <a class="new_pet" href=""> <img src="../Resources/img/ui_icons/plus.png" id="add" alt="Filtres">  Nouvelle Pétition</a>
</div>

<?php
$card_num = 5
?>

<div class="pet_container">
    <?php
    for($i=0;$i<$card_num;$i++){
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
            </div>
            <div class="footer_link">
                <a href="" class="mypet desktop">Voir la Pétition</a>
                <a href="" class="mypet mobile">Voir</a>
                <a href="" class="action_btn"><img src="../Resources/img/ui_icons/crayon.png" alt="Modifier la Pétition"></a>
                <a href="" class="action_btn"><img src="../Resources/img/ui_icons/trash.png" alt="Supprimer la Pétition"></a>
            </div>
        </div>';
    }
    ?>
</div>

<?php
include_once 'footer.php'
?>