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

$get_avatar_skin = $pdo->prepare('SELECT avatar_skin FROM USER WHERE id = :id');
$get_avatar_skin->bindParam(':id', $_GET['id']);
$get_avatar_skin->execute();
$avatar_skin = $get_avatar_skin->fetchColumn();

$get_avatar_hat_color = $pdo->prepare('SELECT avatar_hat_color FROM USER WHERE id = :id');
$get_avatar_hat_color->bindParam(':id', $_GET['id']);
$get_avatar_hat_color->execute();
$avatar_hat_color = $get_avatar_hat_color->fetchColumn();

$get_avatar_eyes_color = $pdo->prepare('SELECT avatar_eyes_color FROM USER WHERE id = :id');
$get_avatar_eyes_color->bindParam(':id', $_GET['id']);
$get_avatar_eyes_color->execute();
$avatar_eyes_color = $get_avatar_eyes_color->fetchColumn();

$get_avatar_mouth_color = $pdo->prepare('SELECT avatar_mouth_color FROM USER WHERE id = :id');
$get_avatar_mouth_color->bindParam(':id', $_GET['id']);
$get_avatar_mouth_color->execute();
$avatar_mouth_color = $get_avatar_mouth_color->fetchColumn();

$get_avatar_skin_color = $pdo->prepare('SELECT avatar_skin_color FROM USER WHERE id = :id');
$get_avatar_skin_color->bindParam(':id', $_GET['id']);
$get_avatar_skin_color->execute();
$avatar_skin_color = $get_avatar_skin_color->fetchColumn();

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

$user_number_of_signature = $pdo->prepare('SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :id');
$user_number_of_signature->bindParam(':id', $_GET['id']);
$user_number_of_signature->execute();
$number_of_signature = $user_number_of_signature->fetchColumn();

if ($number_of_signature === false) {
    $number_of_signature = 0;
}

$user_ban = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :id");
$user_ban->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$user_ban->execute();
$ban = $user_ban->fetchColumn();

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
            if($ban > 0){
                echo '<p class="admin_tag">Compte Banni</p>';
            }
            ?>
        </div>
    </div>
    <div class="avatar">
        <img class="skin" src="../Resources/avatar/skin/skin<?=$avatar_skin?>c<?=$avatar_skin_color?>.png" alt="">
        <img src="../Resources/avatar/hat/hat<?=$avatar_hat?>c<?=$avatar_hat_color?>.png" class="hat" alt="Hat" id="hat">
        <img src="../Resources/avatar/eyes/eye<?=$avatar_eyes?>c<?=$avatar_eyes_color?>.png" class="eyes" alt="Eyes" id="eyes">
        <img src="../Resources/avatar/mouth/smile<?=$avatar_mouth?>c<?=$avatar_mouth_color?>.png" class="mouth" alt="Mouth" id="mouth">
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
    <p class="stat_value_container"><?=$number_of_signature?> Signatures</p>
    <p class="stat_value_container">X € De dons</p>
</div>
    </div>
</div>

<?php
include_once 'footer.php'
?>