<?php

include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    $user = 'Anonyme';
}else{
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $_SESSION['mail']);
    $stmt->execute();
    $user = $stmt->fetchColumn();
}

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'D2SC0V', $user, $user_ip, 'Visite de la page "Découverte utilisateur"');

$user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$user_id_stmt->execute();
$user_id = $user_id_stmt->fetchColumn();

?>

<link rel="stylesheet" href="css/discover.css">
<link rel="stylesheet" href="css/view_petition.css">

<div class="user_container">

    <?php
    $get_all_users = $pdo->prepare("SELECT username, user_daily_status, id, avatar_hat, avatar_eyes, avatar_mouth, avatar_skin, avatar_hat_color, avatar_eyes_color, avatar_mouth_color, avatar_skin_color, user_public, is_admin, is_benevole FROM USER");
    $get_all_users->execute();
    $all_users = $get_all_users->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_users as $user) {
        
        $hat = $user['avatar_hat'];
        $eyes = $user['avatar_eyes'];
        $mouth = $user['avatar_mouth'];
        $skin = $user['avatar_skin'];   

        $avatar_hat_color = $user['avatar_hat_color'];
        $avatar_eyes_color = $user['avatar_eyes_color'];
        $avatar_mouth_color = $user['avatar_mouth_color'];
        $avatar_skin_color = $user['avatar_skin_color'];

        $hat = $hat . 'c' . $avatar_hat_color;
        $eyes = $eyes . 'c' . $avatar_eyes_color;
        $mouth = $mouth . 'c' . $avatar_mouth_color;
        $skin = $skin . 'c' . $avatar_skin_color;

        $check_if_user_is_banned_stmt = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :user_id");
        $check_if_user_is_banned_stmt->bindParam(':user_id', $user['id']);
        $check_if_user_is_banned_stmt->execute();
        $is_banned = $check_if_user_is_banned_stmt->fetchColumn();

        if ($is_banned > 0) {
            continue;
        }

        echo '<div class="user_item' . ($user_id == $user['id'] ? ' my_profile' : '') . '" onclick="window.location.href=\'view_profile.php?id='. $user['id'] .'\'">';
        echo '<div class="left">';
        echo '<div class="avatar">';
        echo '<img class="skin" src="../Resources/avatar/skin/skin'. htmlspecialchars($skin) .'.png" alt="">';
        echo '<img src="../Resources/avatar/hat/hat' . htmlspecialchars($hat) . '.png" class="hat" alt="Hat" id="hat">';
        echo '<img src="../Resources/avatar/eyes/eye' . htmlspecialchars($eyes) . '.png" class="eyes" alt="Eyes" id="eyes">';
        echo '<img src="../Resources/avatar/mouth/smile' . htmlspecialchars($mouth) . '.png" class="mouth" alt="Mouth" id="mouth">';
        echo '</div>';
        echo '</div>';
        echo '<div class="right">';
        echo '<p class="username">' . html_entity_decode($user['username']) . '&nbsp;&nbsp;';
        if ($user['is_admin'] == 1) {
            echo '<span class="tag" title="Administrateur"><img src="/Resources/img/ui_icons/admin.png" alt="Admin"></span>';
        }
        if ($user['is_benevole'] == 1) {
            echo '<span class="tag" title="Bénévole"><img src="/Resources/img/ui_icons/volunteer.png" alt="Bénévole"></span>';
        }
        if ($user['user_public'] == 0) {
            echo '<span class="tag" title="Compte privé"><img src="/Resources/img/ui_icons/invisible.png" alt="Compte privé"></span>';
        }
        echo '</p>';

        if($user['user_public'] == 1){
            echo '<p class="statut">' . html_entity_decode($user['user_daily_status']) . '</p>';
        }

        if($user_id != $user['id']){
            echo '<a href="Processus/add_sfriend.php?uid='. $user['id'] .'"><img src="/Resources/img/ui_icons/friend_request.png" alt=""></a>';
        }

        echo '</div>';
        echo '</div>';
    }


    ?>

</div>

<script src="js/user_partial_load.js"></script>

<?php
include_once 'footer.php';
?>