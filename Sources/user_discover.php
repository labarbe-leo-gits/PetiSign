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


?>

<link rel="stylesheet" href="css/discover.css">
<link rel="stylesheet" href="css/view_petition.css">

<div class="user_container">

    <?php
    $get_all_users = $pdo->prepare("SELECT username, user_daily_status, id, avatar_hat, avatar_eyes, avatar_mouth, avatar_skin, avatar_hat_color, avatar_eyes_color, avatar_mouth_color, avatar_skin_color FROM USER");
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

        echo '<div class="user_item" onclick="window.location.href=\'view_profile.php?id='. $user['id'] .'\'">';
        echo '<div class="left">';
        echo '<div class="avatar">';
        echo '<img class="skin" src="../Resources/avatar/skin/skin'. htmlspecialchars($skin) .'.png" alt="">';
        echo '<img src="../Resources/avatar/hat/hat' . htmlspecialchars($hat) . '.png" class="hat" alt="Hat" id="hat">';
        echo '<img src="../Resources/avatar/eyes/eye' . htmlspecialchars($eyes) . '.png" class="eyes" alt="Eyes" id="eyes">';
        echo '<img src="../Resources/avatar/mouth/smile' . htmlspecialchars($mouth) . '.png" class="mouth" alt="Mouth" id="mouth">';
        echo '</div>';
        echo '</div>';
        echo '<div class="right">';
        echo '<p class="username">' . html_entity_decode($user['username']) . '</p>';
        echo '<p class="statut">' . html_entity_decode($user['user_daily_status']) . '</p>';
        echo '<a href="Processus/add_friend.php?uid='. $user['id'] .'"><img src="/Resources/img/ui_icons/friend_request.png" alt=""></a>';
        echo '</div>';
        echo '</div>';
    }


    ?>

</div>

<script src="js/user_partial_load.js"></script>

<?php
include_once 'footer.php';
?>