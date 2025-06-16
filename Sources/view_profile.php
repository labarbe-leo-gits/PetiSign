<?php

if($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

include_once 'header.php';
include_once 'checker.php';

?>

<link rel="stylesheet" href="css/profile.css">
<link rel="stylesheet" href="css/discover.css">
<link rel="stylesheet" href="css/view_profile.css">

<?php

if((!is_numeric($_GET['id']) || $_GET['id'] <= 0) && $_GET['id'] != "AnonymousUsr") {
    echo "Invalid user ID.";
    exit;
}

$get_username_by_id_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$get_username_by_id_stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$get_username_by_id_stmt->execute();
$get_username_by_id = $get_username_by_id_stmt->fetch(PDO::FETCH_ASSOC);

if ($get_username_by_id) {
    $username = $get_username_by_id['username'];
}
 else {
    echo "User not found.";
    exit;
}

$get_user_public_stmt = $pdo->prepare('SELECT user_public FROM USER WHERE id = :id');
$get_user_public_stmt->bindParam(':id', $_GET['id']);
$get_user_public_stmt->execute();
$user_public = $get_user_public_stmt->fetchColumn();

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

$check_if_logged_user_is_friend_with_target = $pdo->prepare("SELECT COUNT(*) FROM FRIEND WHERE (id_user = :id_user AND id_friend = :id_target) OR (id_user = :id_target AND id_friend = :id_user)");
$check_if_logged_user_is_friend_with_target->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$check_if_logged_user_is_friend_with_target->bindParam(':id_target', $_GET['id'], PDO::PARAM_INT);
$check_if_logged_user_is_friend_with_target->execute();
$is_friend = $check_if_logged_user_is_friend_with_target->fetchColumn();


$check_if_user_blocked_target = $pdo->prepare("SELECT COUNT(*) FROM BLOCKED_USER WHERE id_user = :id_user AND id_blocked_user = :id_target");
$check_if_user_blocked_target->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$check_if_user_blocked_target->bindParam(':id_target', $_GET['id'], PDO::PARAM_INT);
$check_if_user_blocked_target->execute();
$is_blocked = $check_if_user_blocked_target->fetchColumn();

$check_if_i_have_been_blocked = $pdo->prepare("SELECT COUNT(*) FROM BLOCKED_USER WHERE id_user = :id_target AND id_blocked_user = :id_user");
$check_if_i_have_been_blocked->bindParam(':id_target', $_GET['id'], PDO::PARAM_INT);
$check_if_i_have_been_blocked->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$check_if_i_have_been_blocked->execute();
$is_i_blocked = $check_if_i_have_been_blocked->fetchColumn();

if($is_blocked > 0 || $is_i_blocked > 0) {
    echo "
        
        <div class='profile_header'>
            <div class='informations'>
                <h2 class='username'>".$username."</h2>
                <div class='user_tags'>
                    <p class='admin_tag'>Utilisateur bloqué</p>
                </div>
            </div>
            <div class='avatar'>
                <img class='skin' src='../Resources/avatar/skin/skin" . $avatar_skin . "c" . $avatar_skin_color . ".png' alt=''>
                <img src='../Resources/avatar/hat/hat" . $avatar_hat . "c" . $avatar_hat_color . ".png' class='hat' alt='Hat' id='hat'>
                <img src='../Resources/avatar/eyes/eye" . $avatar_eyes . "c" . $avatar_eyes_color . ".png' class='eyes' alt='Eyes' id='eyes'>
                <img src='../Resources/avatar/mouth/smile" . $avatar_mouth . "c" . $avatar_mouth_color . ".png' class='mouth' alt='Mouth' id='mouth'>
            </div>
        </div>";

        if($is_blocked > 0) {

            echo "
            <div class='disclaimer'>
                <p>Vous avez bloqué cet utilisateur. Débloquez le pour consulter son profil ou lui envoyer des messages !</p>
            </div>

            <div class='button_container'>
                <button class='custom-button' onclick=\"window.location.href='Processus/unblock_user.php?uid=". $_GET['id'] ."'\">Débloquer</button>
                <button class='custom-button' onclick=\"window.location.href='index.php'\">Retour à l'accueil</button>
            </div>
            
            ";
        } else if($is_i_blocked > 0) {
            echo "
            <div class='disclaimer'>
                <p>Vous avez été bloqué par cet utilisateur. Vous ne pouvez pas consulter son profil.</p>
            </div>

            <div class='button_container'>
                <button class='custom-button' onclick=\"window.location.href='index.php'\">Retour à l'accueil</button>
            </div>
            
            ";
        }
    exit;
}

if($user_public == 0 && ($user_id != $_GET['id']) && $is_admin != 1 && $is_friend == 0) {
        $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        echo "
        
        <div class='profile_header'>
            <div class='informations'>
                <h2 class='username'>".$username."</h2>
                <div class='user_tags'>
                    <p class='admin_tag'>Profil privé</p>
                </div>
            </div>
            <div class='avatar'>
                <img class='skin' src='../Resources/avatar/skin/skin" . $avatar_skin . "c" . $avatar_skin_color . ".png' alt=''>
                <img src='../Resources/avatar/hat/hat" . $avatar_hat . "c" . $avatar_hat_color . ".png' class='hat' alt='Hat' id='hat'>
                <img src='../Resources/avatar/eyes/eye" . $avatar_eyes . "c" . $avatar_eyes_color . ".png' class='eyes' alt='Eyes' id='eyes'>
                <img src='../Resources/avatar/mouth/smile" . $avatar_mouth . "c" . $avatar_mouth_color . ".png' class='mouth' alt='Mouth' id='mouth'>
            </div>
        </div>
        <div class='disclaimer'>
            <p>Ce profil est privé. Vous ne pouvez pas le consulter.</p>
        </div>

        <div class='button_container'>
            <button class='custom-button' onclick=\"window.location.href='$referer'\">Ajouter un ami</button>
            <button class='custom-button' onclick=\"window.location.href='index.php'\">Retour à l'accueil</button>
        </div>
        
        ";

        exit;
    
}

if($user_public == 0 && $user_id == $_GET['id']) {
    echo "
    <div class='disclaimer'>
    <p>Votre profil est privé. Vous seul, vos amis, (ainsi que les administrateurs de la plateforme) pouvez le consulter.</p>
</div>
    ";
}

if($user_public == 0 && $is_admin == 1 && $user_id != $_GET['id']) {
    echo "
    <div class='disclaimer'>
    <p>Cet utilisateur a défini son paramètre de confidentialité sur privé.</p>
</div>
    ";
}

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

if(isset($_SESSION['mail'])){
    $get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $get_user_id->bindParam(':mail', $_SESSION['mail']);
    $get_user_id->execute();
    $user_id = $get_user_id->fetchColumn();
}else{
    $user_id = null;
}

$check_if_friend_request_was_already_sent = $pdo->prepare("SELECT COUNT(id) FROM USER_CANDIDATE WHERE id_user = :id_user AND target_user = :id_target AND current_status = 'En Attente'");
$check_if_friend_request_was_already_sent->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$check_if_friend_request_was_already_sent->bindParam(':id_target', $_GET['id'], PDO::PARAM_INT);
$check_if_friend_request_was_already_sent->execute();
$friend_request_already_sent = $check_if_friend_request_was_already_sent->fetchColumn();

$count_pending_requests = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE target_user = :id AND current_status = 'En Attente'");
$count_pending_requests->bindParam(':id', $user_id, PDO::PARAM_INT);
$count_pending_requests->execute();
$pending_requests_count = $count_pending_requests->fetchColumn();

?>

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
            if($is_benevole != 1 && $is_admin != 1 && $ban <= 0){
                echo '<p class="admin_tag empty_tag"></p>';
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

    <?php
        $check_if_current_user_already_reported_target = $pdo->prepare("SELECT COUNT(*) FROM REPORT WHERE id_target = :id_target AND id_user = :id_user AND report_type = 1");
        $check_if_current_user_already_reported_target->bindParam(':id_target', $_GET['id'], PDO::PARAM_INT);
        $check_if_current_user_already_reported_target->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $check_if_current_user_already_reported_target->execute();
        $already_reported = $check_if_current_user_already_reported_target->fetchColumn();

        if($ban <= 0){

            if($user_id != $_GET['id']){
                if($already_reported <= 0){
                    echo "
                    <div class='report'>
                        <a href='Processus/report.php?id=". $_GET['id'] ."&type=1' class='report_btn' id='report'><img src='/Resources/img/ui_icons/red-flag.png' alt=''></a>
                    </div>
                    ";
                    echo "
                <div class='report report_second' onclick='window.location.href=\"Processus/add_friend.php?uid=" . $_GET['id'] . "\"'>
                    <a class='report_btn' href='Processus/add_friend.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/friend_request.png' alt='Ajouter un ami'></a>
                </div>
                                    <div class='report report_third' onclick='window.location.href=\"Processus/block_user.php?uid=". $_GET['id'] ."\"'>
                        <a class='report_btn' href='Processus/block_user.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/ban.png' alt='Bloquer'></a>
                    </div>
                ";
                }else{
                    echo "
                    <div class='report disabled_report'>
                        <a class='report_btn' id='report' disabled><img src='/Resources/img/ui_icons/red-flag.png' alt=''></a>
                    </div>
                    ";
                }
                if($friend_request_already_sent <= 0 && $is_friend == 0){
                    $check_if_target_user_already_sent_a_request = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE id_user = :id_user AND target_user = :id_target AND current_status = 'En Attente'");
                    $check_if_target_user_already_sent_a_request->bindParam(':id_user', $_GET['id'], PDO::PARAM_INT);
                    $check_if_target_user_already_sent_a_request->bindParam(':id_target', $user_id, PDO::PARAM_INT);
                    $check_if_target_user_already_sent_a_request->execute();
                    $target_request_already_sent = $check_if_target_user_already_sent_a_request->fetchColumn();
                    if($target_request_already_sent <= 0){
                        echo "
                        <div class='report report_second' onclick='window.location.href=\"Processus/add_friend.php?uid=" . $_GET['id'] . "\"'>
                            <a class='report_btn' href='Processus/add_friend.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/friend_request.png' alt='Ajouter un ami'></a>
                        </div>
                        
                        ";
                    }else if($target_request_already_sent > 0){
                        echo "
                        <div class='report report_second' onclick='window.location.href=\"Processus/add_friend.php?uid=" . $_GET['id'] . "\"'>
                            <a class='report_btn' href='Processus/add_friend.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/validate.png' alt='Ajouter un ami'></a>
                        </div>
                                            <div class='report report_third' onclick='window.location.href=\"Processus/block_user.php?uid=". $_GET['id'] ."\"'>
                        <a class='report_btn' href='Processus/block_user.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/ban.png' alt='Bloquer'></a>
                    </div>
                        ";
                    }
                }else if($friend_request_already_sent > 0){
                    echo "
                    <div class='report report_second disabled_report' title='Demande envoyée'>
                        <a class='report_btn' id='report' disabled><img src='/Resources/img/ui_icons/pending.png' alt='Demande d\'ami déjà envoyée'></a>
                    </div>
                    ";
                }else{
                    echo "
                    <div class='report report_second' onclick='window.location.href=\"manage_friends.php\"'>
                        <a class='report_btn' href='manage_friends.php'><img src='/Resources/img/ui_icons/friend.png' alt='Gérer les amis'></a>
                    </div>
                    <div class='report report_third' onclick='window.location.href=\"Processus/block_user.php?uid=". $_GET['id'] ."\"'>
                        <a class='report_btn' href='Processus/block_user.php?uid=". $_GET['id'] ."'><img src='/Resources/img/ui_icons/ban.png' alt='Bloquer'></a>
                    </div>
                    ";
                }
            
            }else{
                echo "
                <div class='report' onclick='window.location.href=\"profile.php\"'>
                    <a class='report_btn' href='profile.php'><img src='/Resources/img/ui_icons/crayon.png' alt='Modifier mon profil'></a>
                </div>
                ";
                echo "
                <div class='report report_second txt_rep' onclick='window.location.href=\"manage_friends.php\"'>
                    <a class='report_btn' href='manage_friends.php'><img src='/Resources/img/ui_icons/friend.png' alt='Ajouter un ami'><p class='friend_text_not'>&nbsp;&nbsp;&nbsp; |&nbsp;&nbsp;&nbsp; Demandes en attente : ". $pending_requests_count ."</p></a>
                </div>
                ";
            }
        }

    ?>

</div>

<?php

$get_user_daily_status = $pdo->prepare("SELECT user_daily_status FROM USER WHERE id = :id");
$get_user_daily_status->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$get_user_daily_status->execute();  
$user_daily_status = $get_user_daily_status->fetchColumn();

if($user_daily_status && $user_daily_status != "" && $user_daily_status != NULL) {
    $status = html_entity_decode($user_daily_status);
}else if($user_id == $_GET['id']) {
    $status = "Vous n'avez pas de statut, ajoutez en un !";
}

?>

<?php if(($user_daily_status && $user_daily_status != "" && $user_daily_status != NULL) || ($user_id == $_GET['id'])): ?>
<div class="user_status">
    <div class="status_container">
        <p class="status_text"><?=$status?></p>
    </div>
    <?php
    if($user_id == $_GET['id']) {
        echo '
        <div class="status_action">
        <a href="javascript:void(0)" class="quick_status_action" onclick="openStatusPopup()"><img src="/Resources/img/ui_icons/crayon.png" alt="Modifier"></a>
        <a href="Processus/clear_status.php" class="quick_status_action"><img src="/Resources/img/ui_icons/trash.png" alt="Supprimer"></a>
    </div>
        ';
    }
    ?>
</div>
<?php endif ?>

<div class="user_public_information">
    <div class="description">
        <div class="desc_header">
            <h2 class="profile_title">Description</h2>
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
    <p class="stat_value_container second_stat"><?=$number_of_signature?> Signatures</p>
</div>

<?php if($user_id != $_GET['id']): ?>
<div class="user_available_infos">
    <div class="petition_card">
        <div class="desc_header">
            <h2 class="profile_title dropdown-toggle" onclick="toggleDropdown()">
                <img src="/Resources/img/ui_icons/greater.png" alt="arrow" class="dropdown-arrow">
                Pétitions
            </h2>
            <hr class="profile_hr" id="third">
        </div>
        <div class="dropdown-content" id="petitionsDropdown">
            <?php
            $get_petitions = $pdo->prepare("SELECT id, title, image_id FROM PETITION WHERE user = :id");
            $get_petitions->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $get_petitions->execute();
            $petitions = $get_petitions->fetchAll(PDO::FETCH_ASSOC);

            if (count($petitions) > 0) {
                foreach ($petitions as $petition) {
                    echo '<div class="card">
                <div class="cardheader">
                    <img src="../Resources/img/petition_selection/' . $petition['image_id'] . '.jpg" alt="">
                </div>
                <div class="cardcontent">
                    <div class="left">
                        <h3>' . html_entity_decode($petition['title']) . '</h3>
                    </div>
                    <div class="right">
                        <a href="view_petition.php?id=' . $petition['id'] . '">Découvrir</a>
                    </div>
                </div>
            </div>';
                }
            } else {
                echo '<p>Aucune pétition trouvée pour cet utilisateur.</p>';
            }
            ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if($user_id != $_GET['id']): ?>
<div class="description contact_div">
        <div class="desc_header">
            <h2 class="profile_title">Contact</h2>
            <hr class="profile_hr">
        </div>
        <?php

        if($ban > 0){
            echo '<p class="user_description">Cet utilisateur est banni, vous ne pouvez pas le contacter.</p>';
        } else {
            if($user_id == $_GET['id']){
                echo '<p class="user_description">Vous ne pouvez pas vous contacter vous-même.</p>';
            } else {
                echo '
                <form action="Processus/create_chat_feed.php" method="POST" class="contact_form">
                    <input type="hidden" name="user_id" id="user_id" value="'. $_GET['id'] .'" required>
                    <button type="submit" class="custom-button contact_btn">Envoyer un message privé</button>
                </form>
                ';
            }
        }

        ?>
    </div> 
    </div>
</div>
<?php endif; ?>

<?php if($user_id == $_GET['id']): ?>

<button type="button" class="custom-button loginbtn logout_desk" onclick="window.location.href='logout.php';">Déconnexion</button>

<div class="status_update_popup" id="statusUpdatePopup" onclick="closeStatusPopup()">
    <div class="popup_content" onclick="event.stopPropagation()">
        <div class="status-popup-header">
            <h2>Mettre à jour votre statut</h2>
            <button class="close_popup" onclick="closeStatusPopup()" title="Fermer">
                <img src="/Resources/img/ui_icons/plus.png" alt="Fermer">
            </button>
        </div>
        <form action="Processus/update_status.php" method="POST">
            <textarea name="status" id="status" placeholder="Entrez votre nouveau statut ici..." maxlength=60 required><?php echo $user_daily_status ?></textarea>
            <input type="hidden" name="user_id" value="<?=$_GET['id']?>">
            <button type="submit" class="custom-button">Mettre à jour</button>
        </form>
    </div>
</div>

<script src="js/status_popup.js"></script>

<?php endif; ?>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('petitionsDropdown');
        const toggle = document.querySelector('.profile_title.dropdown-toggle');
        const arrow = document.querySelector('.dropdown-arrow');
        
        dropdown.classList.toggle('show');
        toggle.classList.toggle('active');
    }
</script>
<?php
include_once 'footer.php'
?>