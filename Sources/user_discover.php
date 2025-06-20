<?php

include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    $user = 'Anonyme';
    $current_observer = 'Anonyme';
}else{
    $stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
    $stmt->bindParam(':mail', $_SESSION['mail']);
    $stmt->execute();
    $user = $stmt->fetchColumn();
    $current_observer = $user;
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
<link rel="stylesheet" href="css/searchbar.css">

<div class="search-wrapper">
    <form method="get" action="user_discover.php" class="search">
        <div class="search-container">
            <input type="text" id="query" name="search" placeholder=" " value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <label for="query">Rechercher un utilisateur...</label>
            <div class="separator"></div>
            <button type="submit">
                <img src="../Resources/img/ui_icons/loupe.png" alt="Search">
            </button>
        </div>
    </form>
</div>

<div class="user_container">
    <div id="no-users-message">
        Aucun utilisateur trouvé.
    </div>

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

        $check_if_i_blocked_the_user = $pdo->prepare("SELECT COUNT(*) FROM BLOCKED_USER WHERE id_user = :user_id AND id_blocked_user = :blocked_id");
        $check_if_i_blocked_the_user->bindParam(':user_id', $user_id);
        $check_if_i_blocked_the_user->bindParam(':blocked_id', $user['id']);
        $check_if_i_blocked_the_user->execute();
        $is_blocked = $check_if_i_blocked_the_user->fetchColumn();

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
        if ($user['is_admin'] == 1 && $is_blocked != 1) {
            echo '<span class="tag" title="Administrateur"><img src="/Resources/img/ui_icons/admin.png" alt="Admin"></span>';
        }
        if ($user['is_benevole'] == 1 && $is_blocked != 1) {
            echo '<span class="tag" title="Bénévole"><img src="/Resources/img/ui_icons/volunteer.png" alt="Bénévole"></span>';
        }
        if ($user['user_public'] == 0 && $is_blocked != 1) {
            echo '<span class="tag" title="Compte privé"><img src="/Resources/img/ui_icons/invisible.png" alt="Compte privé"></span>';
        }
        echo '</p>';

        if($user['user_public'] == 1 && $is_blocked != 1){
            echo '<p class="statut">' . html_entity_decode($user['user_daily_status']) . '</p>';
        }

        if($user_id != $user['id'] && $current_observer == 'Anonyme'){
            echo '<a href="Processus/add_friend.php?uid='. $user['id'] .'"><img src="/Resources/img/ui_icons/friend_request.png" alt=""></a>';
        }else if($user_id != $user['id']){
            $check_if_logged_user_is_friend_stmt = $pdo->prepare("SELECT COUNT(*) FROM FRIEND WHERE (id_user = :user_id AND id_friend = :friend_id) OR (id_user = :friend_id AND id_friend = :user_id)");
            $check_if_logged_user_is_friend_stmt->bindParam(':user_id', $user_id);
            $check_if_logged_user_is_friend_stmt->bindParam(':friend_id', $user['id']);
            $check_if_logged_user_is_friend_stmt->execute();
            $is_friend = $check_if_logged_user_is_friend_stmt->fetchColumn();

            if ($is_friend > 0) {
                echo '<a href="view_profile.php?id='. $user['id'] .'"><img src="/Resources/img/ui_icons/eye.png" alt="Retirer" class="cross_img"></a>';
            } else if($is_blocked > 0){
                echo '<a href="Processus/unblock_user.php?uid='. $user['id'] .'"><img src="/Resources/img/ui_icons/ban.png" alt=""></a>';
            }
            else {
                echo '<a href="Processus/add_friend.php?uid='. $user['id'] .'"><img src="/Resources/img/ui_icons/friend_request.png" alt=""></a>';
            }
        }

        echo '</div>';
        echo '</div>';
    }

    ?>

</div>

<script src="js/user_partial_load.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('query');
    const userContainer = document.querySelector('.user_container');
    const noUsersMessage = document.getElementById('no-users-message');

    function performSearch(searchTerm) {
        const users = userContainer.querySelectorAll('.user_item');
        let visibleUsers = 0;
        
        users.forEach(user => {
            const username = user.querySelector('.username').textContent.toLowerCase();
            if (username.includes(searchTerm)) {
                user.style.display = '';
                visibleUsers++;
            } else {
                user.style.display = 'none';
            }
        });

        if (visibleUsers === 0 && searchTerm !== '') {
            noUsersMessage.style.display = 'block';
        } else {
            noUsersMessage.style.display = 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            performSearch(searchTerm);
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search')) {
        const searchValue = urlParams.get('search');
        if (searchInput) {
            searchInput.value = searchValue;
            performSearch(searchValue.toLowerCase());
        }
    }

    window.addEventListener('beforeunload', function() {
        const newUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, document.title, newUrl);
    });
});

</script>

<?php
include_once 'footer.php';
?>