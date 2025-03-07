<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="css/view_petition.css">

<script src="js/dynamic_underline_view.js"></script>

<div class="page_container">

    <div class="main_container">
        <div class="petition_header">
            <p>&nbsp;</p>
            <div class="text">
                <h1 class="petition_name highlighted-text" id="lmm">Nom de la Pétition</h1>
                <a class="pet_category" href="">Catégorie</a>
            </div>
        </div>

        <div class="description">
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Saepe corporis ducimus, aliquid doloremque nesciunt quasi sunt dolorem veniam eum ipsam libero. Inventore voluptas error rem architecto quisquam unde sequi hic! Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam qui hic sapiente perferendis, in quasi consectetur, ex est sint reiciendis necessitatibus natus atque impedit neque cum molestias quos beatae eum. Lorem ipsum dolor sit, amet consectetur adipisicing elit. Et, aliquam odio. Mollitia quae cum iste dicta in cumque facere sint labore nostrum nesciunt odio, delectus ea, laudantium possimus, fugit architecto.</p>
        </div>
    </div>

    <div class="grid">
        <div class="signatures_container">
            <div class="objectif">XXXX / XXXX Signatures récoltées</div>
            <div class="sign">
                <form method="post" action="sign.php">
                    <button type="submit" class="sign_petition_btn">Je Signe !</button>
                </form></div>
        </div>

        <div class="petition_information">
            <p class="author">AUTEUR</p>
            <p class="creation_date">Publiée le JJ//MM/YYYY</p>
        </div>
    </div>

    <div class="action_btn">
        <button class="custom-button" onclick="window.history.back()" id="back">Revenir en arrière</button>
    </div>
</div>
<?php
include_once 'footer.php';
?>
