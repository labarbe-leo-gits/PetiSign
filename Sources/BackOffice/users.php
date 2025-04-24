<?php
include_once 'header.php';

$get_current_admin_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_current_admin_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_current_admin_id->execute();
$current_admin_id = $get_current_admin_id->fetchColumn();

$error_details = '';
$success_details = '';

if(isset($_GET['error']) && isset($_GET['referer'])){

    $json_file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Sources/json/error_register.json');
    $error_manager = json_decode($json_file, true);
    
    if(array_key_exists($_GET['error'], $error_manager)){
        $error_details = $error_manager[$_GET['error']];
    } else {
        $error_details = "Unknown error";
    }
}

if(isset($_GET['success']) && isset($_GET['referer'])){

    $json_file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Sources/json/success_register.json');
    $success_manager = json_decode($json_file, true);
    
    if(array_key_exists($_GET['success'], $success_manager)){
        $success_details = $success_manager[$_GET['success']];
    } else {
        $success_details = "Unknown success message";
    }
}
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion des utilisateurs</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
        <a class="captcha_database_action" href="create_admin.php"><img src="../../Resources/img/ui_icons/plus.png" alt="Ajouter un admin"> Ajouter un compte administrateur</a>
        <a class="captcha_database_action" href="Processus/export_users.php" target="_blank"><img src="../../Resources/img/ui_icons/download.png" alt="Exporter utilisateurs">&nbsp;&nbsp;Extraire la liste utilisateur
</a>

</a>


    </div>
    <div class="message">
    <?php
        if(isset($_GET['error']) && isset($_GET['referer'])){
            echo '
            <div class="error">
                <p>' . $error_details .'</p>
            </div>
            ';
        }
        if(isset($_GET['success']) && isset($_GET['referer'])){
            echo '
            <div class="success">
                <p>' . $success_details .'</p>
            </div>
            ';
        }
    ?>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
                <th>Rôle(s)</th>
                <th>Actions</th>
            </tr>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM USER");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($users as $user){

                    $user_ban = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :id");
                    $user_ban->bindParam(':id', $user['id'], PDO::PARAM_INT);
                    $user_ban->execute();
                    $ban = $user_ban->fetchColumn();

                    echo "<tr>";
                    echo "<td class='id ".($ban > 0 ? "banned" : "")."'>".$user['id']."</td>";
                    echo "<td class='content ".($ban > 0 ? "banned" : "")."'>".$user['username']."</td>";
                    if($user['is_admin'] == 1){
                        if($user['is_benevole'] == 1){
                            echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Administrateur, Bénévole</td>";
                        } else {
                            echo "<td class='content".($ban > 0 ? "banned" : "")."'>Administrateur</td>";
                        }
                    } else if($user['is_benevole'] == 1){
                        echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Bénévole</td>";
                    } else {
                        echo "<td class='content ".($ban > 0 ? "banned" : "")."'>Utilisateur</td>";
                    }
                    echo "<td class='actions'>";
                    echo "<a href='../view_profile.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/eye.png' alt='Voir'></a>";
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='modify_user_form.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/crayon.png' alt='Modify'></a>";
                    echo "<a href='' class='void'>&nbsp;</a>";


                    if($current_admin_id != $user['id']) {
                        echo "<a href='Processus/delete_user.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
                        echo "<a href='' class='void'>&nbsp;</a>";
                    }

                    if($current_admin_id == $user['id']){
                        echo "<a href='' class='void'></a>";
                    } else {
                        if($ban > 0){
                            echo "<a href='Processus/unban_user.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/ban.png' alt='Débannir'></a>";
                        } else {
                            echo "<a href='ban_user_form.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/ban-user.png' alt='Bannir'></a>";
                        }
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                    echo "<tr>";
                    echo "<td class='id'>N/A</td>";
                    echo "<td class='content'>Error</td>";
                    echo "<td class='content'>".$e."</td>";
                    echo "<td class='actions'>";
                    echo "</td>";
                    echo "</tr>";

            }
            ?>
        </table>
    </div>
    <div class="title">
        <h2 class="highlighted-text" id="subtitle">Utilisateurs connectés en temps réel</h2>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom d'utilisateur</th>
            </tr>
            <?php

            $sessions_stmt = $pdo->prepare("SELECT * FROM SESSION");
            $sessions_stmt->execute();
            $sessions = $sessions_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($sessions as $session){
                $user_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                $user_stmt->bindParam(':id', $session['id_user'], PDO::PARAM_INT);
                $user_stmt->execute();
                $user = $user_stmt->fetchColumn();

                echo "<tr>";
                echo "<td class='id'>".$session['id_user']."</td>";
                echo "<td class='content'>".$user."</td>";
                echo "</tr>";
            }

            ?>
        </table>
    </div>
</div>
</div>

<script src="/Sources/js/message_hider.js"></script>

<?php
include_once 'footer.php';
?>
