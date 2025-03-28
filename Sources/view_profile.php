<?php

if($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

include_once 'header.php';
include_once 'database/database.php';

$get_username_by_id_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$get_username_by_id_stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$get_username_by_id_stmt->execute();
$get_username_by_id = $get_username_by_id_stmt->fetch(PDO::FETCH_ASSOC);

if ($get_username_by_id) {
    $username = $get_username_by_id['username'];
} else {
    echo "User not found.";
    exit;
}

$get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE id = :id');
$get_avatar_hat->bindParam(':id', $_GET['id']);
$get_avatar_hat->execute();
$avatar_hat = $get_avatar_hat->fetchColumn();

$get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE id = :id');
$get_avatar_eyes->bindParam(':id', $_GET['id']);
$get_avatar_eyes->execute();
$avatar_eyes = $get_avatar_eyes->fetchColumn();

$get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE id = :id');
$get_avatar_mouth->bindParam(':id', $_GET['id']);
$get_avatar_mouth->execute();
$avatar_mouth = $get_avatar_mouth->fetchColumn();

$get_if_user_is_admin_stmt = $pdo->prepare('SELECT is_admin FROM USER WHERE id = :id');
$get_if_user_is_admin_stmt->bindParam(':id', $_GET['id']);
$get_if_user_is_admin_stmt->execute();
$is_admin = $get_if_user_is_admin_stmt->fetchColumn();

$get_is_benevole_stmt = $pdo->prepare('SELECT is_benevole FROM USER WHERE id = :id');
$get_is_benevole_stmt->bindParam(':id', $_GET['id']);
$get_is_benevole_stmt->execute();
$is_benevole = $get_is_benevole_stmt->fetchColumn();

$get_description = $pdo->prepare('SELECT description FROM USER WHERE id = :id');
$get_description->bindParam(':id', $_GET['id']);
$get_description->execute();
$description = $get_description->fetchColumn();
$description = nl2br($description);

$user_number_of_petitions = $pdo->prepare('SELECT COUNT(*) FROM PETITION WHERE user = :id');
$user_number_of_petitions->bindParam(':id', $_GET['id']);
$user_number_of_petitions->execute();
$number_of_petitions = $user_number_of_petitions->fetchColumn();
if ($number_of_petitions === false) {
    $number_of_petitions = 0;
}

?>

<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/view_profile.css">

<div class="profile_header">
    <div class="informations">
        <h2 class="username"><?=$username?></h2>
        <div class="user_tags">
            <?php
            if ($is_admin == 1) {
                echo '<p class="admin_tag">Administrateur</p>';
            }
            if($is_benevole == 1){
                echo '<p class="admin_tag">Bénévole</p>';
            }
            ?>
        </div>
    </div>
    <div class="avatar">
        <img class="skin" src="../Resources/avatar/skin.png" alt="">
        <img src="../Resources/avatar/hat<?=$avatar_hat?>.png" class="hat" alt="Hat" id="hat">
        <img src="../Resources/avatar/eyes<?=$avatar_eyes?>.png" class="eyes" alt="Eyes" id="eyes">
        <img src="../Resources/avatar/smile<?=$avatar_mouth?>.png" class="mouth" alt="Mouth" id="mouth">
    </div>
</div>

<div class="user_public_information">
    <div class="description">
        <div class="desc_header">
            <h2 class="profile_title">Description de l'utilisateur</h2>
            <hr class="profile_hr">
        </div>
        <p class="user_description"><?=$description?></p>
    </div>
    <div class="statistiques">
        <div class="desc_header">
            <h2 class="profile_title">Statistiques</h2>
            <hr class="profile_hr" id="second">
        </div>
        <div class="stat_container">
            <p class="stat_value_container"><?=$number_of_petitions?> Pétitions crées</p>
            <p class="stat_value_container">X Signatures</p>
            <p class="stat_value_container">X € De dons</p>
        </div>
    </div>
</div>

<?php
include_once 'footer.php'
?>