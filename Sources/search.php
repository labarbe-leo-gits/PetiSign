<?php
include_once 'header.php'
?>

<link rel="stylesheet" href="css/searchbar.css">
<link rel="stylesheet" href="css/result.css">

<?php

if(isset($_GET['query'])){
    $insertVal = $_GET['query'];
}else{
    $insertVal = "";
}

?>

<div class="search-wrapper">
    <form method="get" action="search.php" class="search">
        <div class="search-container">
            <input value="<?=$insertVal?>" type="text" id="query" name="query" placeholder=" ">
            <label for="query">Rechercher ...</label>
            <div class="separator"></div>
            <button type="submit">
                <img src="../Resources/img/ui_icons/loupe.png" alt="Search">
            </button>
        </div>
        <a class="filters" href=""> <img src="../Resources/img/ui_icons/filter.png" id="filter" alt="Filtres">  Filtres</a>
        <!--<div class="filters">  need to make it a popover selector once 'filters' are clicked
            <input type="checkbox" name="animals_filter" id="animals_filter">
            <label for="animals_filter">Animaux</label>
            <input type="checkbox" name="politic_filter" id="politic_filter">
            <label for="politic_filter">Politique</label>
            <input type="checkbox" name="environment_filter" id="environment_filter">
            <label for="environment_filter">Environnement</label>
        </div>-->
    </form>
</div>

<hr>
<?php
if($insertVal == "" or $insertVal == " "){
    echo '';
}else{
    print "<h1 class='querytitle'>Résultats de la recherche pour : &laquo; ".$insertVal." &raquo;</h1>";
}
?>

<?php
include_once 'footer.php';
?>
