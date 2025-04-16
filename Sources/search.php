<?php
include_once 'header.php';
include_once 'database/database.php';
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

<hr id="after_search">
<?php
if($insertVal == "" or $insertVal == " "){
    echo '';
}else{
    print "<h1 class='querytitle'>Résultats de la recherche pour : &laquo; ".$insertVal." &raquo;</h1>";
}
?>
<div class="card-container">

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

<?php
include_once 'footer.php';
?>
