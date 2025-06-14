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

                    echo '<div class="friend">';
                    echo '<p>' . htmlspecialchars($friend_username) . '</p>';
                    echo '<form method="post" action="Processus/requests_manager.php?action=remove">';
                    echo '<input type="hidden" name="friend_id" value="' . htmlspecialchars($friend_id) . '">';
                    echo '<button type="submit">Supprimer</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Aucun ami trouvé.</p>';
            }

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
                    echo '<button type="submit">Accepter</button>';
                    echo '</form>';
                    echo '<form method="post" action="Processus/requests_manager.php?action=decline">';
                    echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($request['id']) . '">';
                    echo '<button type="submit">Rejeter</button>';
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