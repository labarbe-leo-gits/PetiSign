<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

include_once 'Processus/sessionlocked_security.php';

$stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
$stmt->bindParam(':mail', $_SESSION['mail']);
$stmt->execute();
$user = $stmt->fetchColumn();

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'MYS1GN', $user, $user_ip, 'Visite de la page "Mes Signatures"');

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$user_id = $get_user_id_stmt->fetchColumn();

$get_user_signatures_stmt = $pdo->prepare('SELECT * FROM SIGNATURE WHERE id_user = :user_id');
$get_user_signatures_stmt->bindParam(':user_id', $user_id);
$get_user_signatures_stmt->execute();
$signatures = $get_user_signatures_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="css/mypet.css">

<div class="title">
    <h1 class="highlighted-text" id="mysigns">Mes Signatures</h1>
    <hr>
    <a class="new_pet" href="discover.php"> <img src="../Resources/img/ui_icons/loupe.png" id="add" alt="Filtres">  Explorer les Pétitions</a>
</div>

<?php
$card_num = 0;
?>

<div class="pet_container">
    <?php

    try{

        foreach($signatures as $signature){

            $get_pet_name_stmt = $pdo->prepare('SELECT title FROM PETITION WHERE id = :petition_id');
            $get_pet_name_stmt->bindParam(':petition_id', $signature['id_petition']);
            $get_pet_name_stmt->execute();
            $petition = $get_pet_name_stmt->fetchColumn();

            $get_category_id_stmt = $pdo->prepare('SELECT category FROM PETITION WHERE id = :petition_id');
            $get_category_id_stmt->bindParam(':petition_id', $signature['id_petition']);
            $get_category_id_stmt->execute();
            $category_id = $get_category_id_stmt->fetchColumn();

            $get_category_name_stmt = $pdo->prepare('SELECT name FROM CATEGORY WHERE id = :category_id');
            $get_category_name_stmt->bindParam(':category_id', $category_id);
            $get_category_name_stmt->execute();
            $category_name = $get_category_name_stmt->fetchColumn();

            $get_description_stmt = $pdo->prepare('SELECT description FROM PETITION WHERE id = :petition_id');
            $get_description_stmt->bindParam(':petition_id', $signature['id_petition']);
            $get_description_stmt->execute();
            $description = $get_description_stmt->fetchColumn();

            $sign_goal_stmt = $pdo->prepare('SELECT signature_goal FROM PETITION WHERE id = :petition_id');
            $sign_goal_stmt->bindParam(':petition_id', $signature['id_petition']);
            $sign_goal_stmt->execute();
            $sign_goal = $sign_goal_stmt->fetchColumn();

            $sign_count_stmt = $pdo->prepare('SELECT signature_count FROM PETITION WHERE id = :petition_id');
            $sign_count_stmt->bindParam(':petition_id', $signature['id_petition']);
            $sign_count_stmt->execute();
            $sign_count = $sign_count_stmt->fetchColumn();

            $pet_img_id_stmt = $pdo->prepare('SELECT image_id FROM PETITION WHERE id = :petition_id');
            $pet_img_id_stmt->bindParam(':petition_id', $signature['id_petition']);
            $pet_img_id_stmt->execute();
            $pet_img_id = $pet_img_id_stmt->fetchColumn();

            print '
            <div class="sample_pet">
                <div class="header">
                    <img src="../Resources/img/petition_selection/'. $pet_img_id .'.jpg" alt="Image de couverture pétition">
                    <p class="category" onclick="window.location.href=\'search.php?category_id='. $category_id .'\'">' . $category_name . '</p>
                </div>
                <div class="content">
                <p>
                    <h2 class="title">' . html_entity_decode($petition) . '</h2>
                    <hr class="pet_sep">
                    <p class="description">' . nl2br(html_entity_decode($description)) . '</p>
                </div>
                <div class="footer">
                    <p class="sign">'. $sign_count .' / ' . $sign_goal . ' Signatures</p>
                </div>
                <div class="footer_link">
                    <a href="view_petition.php?id=' . $signature['id_petition'] . '" class="mypet desktop">Voir la Pétition</a>
                    <a href="" class="mypet mobile">Voir</a>
                </div>
                </p>
            </div>';
        }
    }catch(PDOException $e){
        echo 'Erreur : ' . $e->getMessage();
    }

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