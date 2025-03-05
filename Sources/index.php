<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/trancho.css">

<div class="page_container">

    <div class="page_title">
        <h1 id="indexhigh" class="highlighted-text">Bienvenue sur PétiSign</h1>
    </div>


    <hr id="main_hr">

    <div class="container">

        <div class="left_img">
            <img id="left_index_img" src="../Resources/img/bg/protest.jpg" alt="Personnes tenant des pancartes">
        </div>

        <div class="buttons">
            <button class="custom-button" id="mainbtn" onclick="location.href='discover.php'" >Explorer Pétisign</button>
            <button class="custom-button" id="subbtn" onclick="show_popup_trancho()">Noter PétiSign</button>
        </div>

        <div class="right_img">
            <img id="right_index_img" src="../Resources/img/bg/manlookingatlaptop.jpg" alt="Un homme en train d'utiliser son ordinateur portable">
        </div>

    </div>

    <div class="filter">&nbsp;</div>

    <div class="container_ popup">
        <div class="left">
            <img id="trancho" src="../Resources/img/trancho/trancho.png" alt="Kevin Trancho">
        </div>
        <div class="close"><img onclick="hide_popup_trancho()" src="../Resources/img/ui_icons/plus.png" alt="Fermer la Popup"></div>
        <div class="right">
            <h1>Noter PétiSign</h1>
            <h2>Quelle est la probabilité que vous recommendiez PétiSign à un ami ?</h2>
            <button class="selectable" id="0" value="0">0</button>
            <?php
                for($i=1;$i<10;$i++){
                    print "<button id='$i' value='0.$i' class='loop selectable'>0.$i</button>";
                }
            ?>
            <button id="10" class="selectable" value="1">1</button>
            <div class="send"><button>Envoyer</button></div>
        </div>
    </div>

</div>

<script src="js/trancho_popup.js"></script>
<script src="js/trancho_elements_logic.js"></script>
<script src="js/trancho_detection.js"></script>

<?php
include_once 'footer.php'
?>
