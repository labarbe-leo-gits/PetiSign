<?php

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: index.php');
    exit();
}

if(isset($_SESSION['mail'])){
    $is_admin = $pdo->prepare('SELECT is_admin FROM USER WHERE mail = :mail');
    $is_admin->bindParam(':mail', $_SESSION['mail']);
    $is_admin->execute();
    $is_admin = $is_admin->fetchColumn();
}

include_once 'header.php';
include_once 'database/database.php';

$validating_petition = $pdo->prepare('SELECT * FROM PETITION WHERE id = :id');
$validating_petition->bindParam(':id', $_GET['id']);
$validating_petition->execute();
$validating_petition = $validating_petition->fetch();

if(!$validating_petition){
    header('Location: index.php');
    exit();
}

$pet_name_stmt = $pdo->prepare('SELECT title FROM PETITION WHERE id = :id');
$pet_name_stmt->bindParam(':id', $_GET['id']);
$pet_name_stmt->execute();
$pet_name = $pet_name_stmt->fetchColumn();

$pet_category_stmt = $pdo->prepare('SELECT category FROM PETITION WHERE id = :id');
$pet_category_stmt->bindParam(':id', $_GET['id']);
$pet_category_stmt->execute();
$pet_category = $pet_category_stmt->fetchColumn();

$pet_category_name = $pdo->prepare('SELECT name FROM CATEGORY WHERE id = :id');
$pet_category_name->bindParam(':id', $pet_category);
$pet_category_name->execute();
$pet_category_name = $pet_category_name->fetchColumn();

$pet_description_stmt = $pdo->prepare('SELECT description FROM PETITION WHERE id = :id');
$pet_description_stmt->bindParam(':id', $_GET['id']);
$pet_description_stmt->execute();
$pet_description = $pet_description_stmt->fetchColumn();

$pet_signature_goal_stmt = $pdo->prepare('SELECT signature_goal FROM PETITION WHERE id = :id');
$pet_signature_goal_stmt->bindParam(':id', $_GET['id']);
$pet_signature_goal_stmt->execute();
$pet_signature_goal = $pet_signature_goal_stmt->fetchColumn();

$pet_author_stmt = $pdo->prepare('SELECT user FROM PETITION WHERE id = :id');
$pet_author_stmt->bindParam(':id', $_GET['id']);
$pet_author_stmt->execute();
$pet_author = $pet_author_stmt->fetchColumn();

$pet_author_username = $pdo->prepare('SELECT username FROM USER WHERE id = :id');
$pet_author_username->bindParam(':id', $pet_author);
$pet_author_username->execute();
$pet_author_username = $pet_author_username->fetchColumn();

$pet_date = $pdo->prepare('SELECT DATE_FORMAT(date, "%d/%m/%Y") FROM PETITION WHERE id = :id');
$pet_date->bindParam(':id', $_GET['id']);
$pet_date->execute();
$pet_date = $pet_date->fetchColumn();

$pet_image_id_stmt = $pdo->prepare('SELECT image_id FROM PETITION WHERE id = :id');
$pet_image_id_stmt->bindParam(':id', $_GET['id']);
$pet_image_id_stmt->execute();
$pet_image_id = $pet_image_id_stmt->fetchColumn();

?>

<link rel="stylesheet" href="css/view_petition.css">

<script src="js/dynamic_underline_view.js"></script>

<div class="page_container">

    <div class="main_container">
        <div class="petition_header">
            <p>&nbsp;</p>
            <div class="text">
                <h1 class="petition_name highlighted-text" id="lmm"><?=$pet_name?></h1>
                <a class="pet_category" href=""><?=$pet_category_name?></a>
            </div>
        </div>

        <div class="description">
            <p><?=$pet_description?></p>
        </div>

        <div class="test">
            <a href="" class="quick">
                <img src="../Resources/img/ui_icons/red-flag.png" alt="">
                &nbsp;Signaler un abus
            </a>

            <?php
            if($is_admin == 1){
                echo '<a href="Processus/admin_delete.php?id='.$_GET['id'].'" class="quick">
                <img src="../Resources/img/ui_icons/trash.png" alt="">&nbsp;Supprimer la pétition (Admin)
            </a>';
            }
            ?>
        </div>
    </div>

    <div class="grid">
        <div class="signatures_container">
            <div class="objectif">XXXX / <?=$pet_signature_goal?> Signatures récoltées</div>
            <div class="sign">
                <form method="post" action="sign.php">
                    <button type="submit" class="sign_petition_btn">Je Signe !</button>
                </form></div>
        </div>

        <div class="petition_information">
            <p class="author">Pétition de <?=$pet_author_username?></p>
            <p class="creation_date">Publiée le <?=$pet_date?></p>
        </div>
    </div>

    <div class="action_btn">
        <button class="custom-button" onclick="window.history.back()" id="back">Revenir en arrière</button>
    </div>
</div>

<style>
    .petition_header {
        background-image: url("../../Resources/img/petition_selection/<?=$pet_image_id?>.jpg");
    }
</style>

<?php
include_once 'footer.php';
?>
