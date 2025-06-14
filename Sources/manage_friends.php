<?php

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

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

write_logs('logs/log.txt', 'MYFR1D', $user, $user_ip, 'Visite de la page "Gestion des amis"');

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$user_id = $get_user_id_stmt->fetchColumn();

$get_all_logged_user_pending_requests_stmt = $pdo->prepare("SELECT * FROM USER_CANDIDATE WHERE target_user = :user_id AND current_status = 'En Attente'");
$get_all_logged_user_pending_requests_stmt->bindParam(':user_id', $user_id);
$get_all_logged_user_pending_requests_stmt->execute();
$get_all_logged_user_pending_requests = $get_all_logged_user_pending_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<link rel="stylesheet" href="css/manage_friends.css">

<div class="container">
    <div class="left">
        <h2>Vos amis</h2>
        <div class="friends_container">
            <?php

            $get_friends_stmt = $pdo->prepare("SELECT * FROM FRIEND WHERE id_user = :user_id OR id_friend = :user_id");
            $get_friends_stmt->bindParam(':user_id', $user_id);
            $get_friends_stmt->execute();
            $friends = $get_friends_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($friends) > 0) {
                foreach ($friends as $friend) {
                    $friend_id = ($friend['id_user'] == $user_id) ? $friend['id_friend'] : $friend['id_user'];
                    $get_friend_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                    $get_friend_username_stmt->bindParam(':id', $friend_id);
                    $get_friend_username_stmt->execute();
                    $friend_username = $get_friend_username_stmt->fetchColumn();

                    $get_friend_user_daily_status = $pdo->prepare("SELECT user_daily_status FROM USER WHERE id = :id");
                    $get_friend_user_daily_status->bindParam(':id', $friend_id);
                    $get_friend_user_daily_status->execute();
                    $friend_daily_status = $get_friend_user_daily_status->fetchColumn();

                    echo '<div class="friend" onclick="window.location.href=\'view_profile.php?id=' . htmlspecialchars($friend_id) . '\'">';

                    $get_avatar_data = $pdo->prepare('
                        SELECT avatar_hat, avatar_eyes, avatar_mouth, avatar_skin, 
                            avatar_hat_color, avatar_eyes_color, avatar_mouth_color, avatar_skin_color 
                        FROM USER WHERE id = :id
                    ');
                    $get_avatar_data->bindParam(':id', $friend_id);
                    $get_avatar_data->execute();
                    $avatar_data = $get_avatar_data->fetch(PDO::FETCH_ASSOC);

                    echo '<div class="friend-info">';
                    echo '<div class="avatar">';
                    echo '<img class="skin" src="../Resources/avatar/skin/skin' . $avatar_data['avatar_skin'] . 'c' . $avatar_data['avatar_skin_color'] . '.png" alt="">';
                    echo '<img src="../Resources/avatar/hat/hat' . $avatar_data['avatar_hat'] . 'c' . $avatar_data['avatar_hat_color'] . '.png" class="hat" alt="Hat">';
                    echo '<img src="../Resources/avatar/eyes/eye' . $avatar_data['avatar_eyes'] . 'c' . $avatar_data['avatar_eyes_color'] . '.png" class="eyes" alt="Eyes">';
                    echo '<img src="../Resources/avatar/mouth/smile' . $avatar_data['avatar_mouth'] . 'c' . $avatar_data['avatar_mouth_color'] . '.png" class="mouth" alt="Mouth">';
                    echo '</div>';

                    echo '<div class="friend-details">';
                    echo '<p>' . htmlspecialchars($friend_username) . '</p>';
                    if ($friend_daily_status) {
                        echo '<p class="daily-status"><i>' . html_entity_decode($friend_daily_status) . '</i></p>';
                    } else {
                        echo '<p class="daily-status"><i>Aucun statut quotidien</i></p>';
                    }
                    echo '</div>';
                    echo '</div>';

                    echo '<form class="btn_form" method="post" action="Processus/requests_manager.php?action=remove">';
                    echo '<input type="hidden" name="friend_id" value="' . htmlspecialchars($friend_id) . '">';
                    echo '<button type="submit" class="friend-remove-btn"><img src="/Resources/img/ui_icons/trash.png" alt="Supprimer"></button>';
                    echo '</form>';

                    echo '</div>';
                }
            } else {
                echo '<p>Aucun ami trouvé.</p>';
            }

            echo "<div class='message'>";
            echo "<button class='add-friend-btn' onclick=\"window.location.href='user_discover.php'\">Ajouter des ami</button>";
            echo "</div>";

            ?>
        </div>
    </div>
    <div class="right">
        <div class="pending">
            <h2>Demandes en attente</h2>
            <?php
            
            if (count($get_all_logged_user_pending_requests) > 0) {
                foreach ($get_all_logged_user_pending_requests as $request) {
                    $requester_id = $request['id_user'];
                    $get_requester_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                    $get_requester_username_stmt->bindParam(':id', $requester_id);
                    $get_requester_username_stmt->execute();
                    $requester_username = $get_requester_username_stmt->fetchColumn();
                    $requester_id = $request['id_user'];

                    echo '<div class="pending_request">';
                    echo '<p>' . htmlspecialchars($requester_username) . ' vous a envoyé une demande d\'ami.</p>';
                    echo '<form method="post" action="Processus/requests_manager.php?action=accept">';
                    echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($request['id']) . '">';
                    echo '<input type="hidden" name="requester_id" value="' . htmlspecialchars($requester_id) . '">';
                    echo '<button type="submit" class="accept-request-btn"><img src="/Resources/img/ui_icons/validate.png" alt="Accepter"></button>';
                    echo '</form>';
                    echo '<form method="post" action="Processus/requests_manager.php?action=decline">';
                    echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($request['id']) . '">';
                    echo '<button type="submit" class="decline-request-btn"><img src="/Resources/img/ui_icons/cross.png" alt="Rejeter"></button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucune demande en attente.</p>';
            }

            ?>
        </div>
        <div class="sent">
            <h2>Demandes envoyées</h2>
            <?php
            $get_all_logged_user_sent_requests_stmt = $pdo->prepare("SELECT * FROM USER_CANDIDATE WHERE id_user = :user_id AND current_status = 'En Attente'");
            $get_all_logged_user_sent_requests_stmt->bindParam(':user_id', $user_id);
            $get_all_logged_user_sent_requests_stmt->execute();
            $get_all_logged_user_sent_requests = $get_all_logged_user_sent_requests_stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($get_all_logged_user_sent_requests) > 0) {
                foreach ($get_all_logged_user_sent_requests as $request) {
                    $target_user_id = $request['target_user'];
                    $get_target_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                    $get_target_username_stmt->bindParam(':id', $target_user_id);
                    $get_target_username_stmt->execute();
                    $target_username = $get_target_username_stmt->fetchColumn();

                    echo '<div class="sent_request">';
                    echo '<p>Vous avez envoyé une demande d\'ami à ' . htmlspecialchars($target_username) . '.</p>';
                    echo '<form method="post" action="Processus/requests_manager.php?action=cancel">';
                    echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($request['id']) . '">';
                    echo '<button type="submit">Annuler la demande</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucune demande envoyée.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php
include_once 'footer.php'
?>