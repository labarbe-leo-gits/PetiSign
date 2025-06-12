<?php
include_once "header.php";
include_once "../database/database.php";
include_once '../checker.php';

$env_file = 'Processus/key.env';
// read env file with parse_ini_file
if (file_exists($env_file)) {
    $env = parse_ini_file($env_file);
} else {
    die("Error: .env file not found.");
}
$admin_key = $env['CrontabKey'] ?? null;

 ?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">

<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Signalements</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page">&nbsp;Actualiser</a>
        <a class="captcha_database_action" href="Processus/delete_all_checked.php"><img src="../../Resources/img/ui_icons/trash.png" alt="Actualiser la page">&nbsp;Effacer les signalements traités</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>Type</th>
                <th>Signaleur</th>
                <th>Cible</th>
                <th>Action</th>
            </tr>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM REPORT");
                $stmt->execute();
                $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($news as $new){

                    $formatted_timestamp = strtotime($new['date']);
                    $formatted_date = date('d/m/Y à H:i', $formatted_timestamp);
                    $new['date'] = $formatted_date;
                    $type = $new['report_type'];

                    if($type == 1){
                        $type = "Utilisateur";
                    } elseif($type == 2){
                        $type = "Pétition";
                    } elseif($type == 3){
                        $type = "Commentaire";
                    } else {
                        $type = "Inconnu";
                    }

                    $user_id = $new['id_user'];
                    $username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id_user");
                    $username_stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
                    $username_stmt->execute();
                    $username = $username_stmt->fetchColumn();

                    $target_id = $new['id_target'];

                    if($new['report_type'] == 1){
                        $target_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id_target");
                        $target_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                        $target_stmt->execute();
                        $target = $target_stmt->fetchColumn();
                        $check_id_user_is_banned_already = $pdo->prepare("SELECT COUNT(id) FROM BAN WHERE id_user = :id_user");
                        $check_id_user_is_banned_already->bindParam(':id_user', $target_id, PDO::PARAM_INT);
                        $check_id_user_is_banned_already->execute();
                        $is_banned = $check_id_user_is_banned_already->fetchColumn();

                    } elseif($new['report_type'] == 2){
                        $target_stmt = $pdo->prepare("SELECT title FROM PETITION WHERE id = :id_target");
                        $target_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                        $target_stmt->execute();
                        $target = $target_stmt->fetchColumn();
                    } elseif($new['report_type'] == 3){
                        $target_stmt = $pdo->prepare("SELECT content FROM COMMENT WHERE id = :id_target");
                        $target_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                        $target_stmt->execute();
                        $target = $target_stmt->fetchColumn();
                    } else {
                        $target_stmt = null;
                    }

                    if($target == null){
                        $target = "Cible supprimée";
                        $update_report_status = $pdo->prepare("UPDATE REPORT SET current_status = 'CLOSED' WHERE id = :id_report");
                        $update_report_status->bindParam(':id_report', $new['id'], PDO::PARAM_INT);
                        $update_report_status->execute();
                    }

                    $go_to_target_link = "";
                    $action_link = "";
                    $target_title = "";
                    if($new['report_type'] == 1){
                        $go_to_target_link = "view_profile.php?id=" . $target_id;
                        $action_link = "Processus/ban.php?user_id=" . $target_id . "&admin_key=" . $admin_key . "&report_id=" . $new['id'];
                        $target_title = "Bannir l'utilisateur";
                    } elseif($new['report_type'] == 2){
                        $go_to_target_link = "view_petition.php?id=" . $target_id;
                        $action_link = "../Processus/delete_petition.php?id=" . $target_id;
                        $target_title = "Supprimer la pétition";
                    }elseif($new['report_type'] == 3){

                        $action_link = "../Processus/delete_com.php?id=" . $target_id;

                        $target_type_stmt = $pdo->prepare("SELECT target_type FROM COMMENT WHERE id = :id_target");
                        $target_type_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                        $target_type_stmt->execute();
                        $target_type = $target_type_stmt->fetchColumn();

                        if($target_type == 1){
                            $petition_stmt = $pdo->prepare("SELECT id_target FROM COMMENT WHERE id = :id_target AND target_type = 1");
                            $petition_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                            $petition_stmt->execute();
                            $petition_id = $petition_stmt->fetchColumn();
                            $go_to_target_link = "view_petition.php?id=" . $petition_id;
                        }else{
                            $petition_stmt = $pdo->prepare("SELECT id_target FROM COMMENT WHERE id = :id_target AND target_type = 2");
                            $petition_stmt->bindParam(':id_target', $target_id, PDO::PARAM_INT);
                            $petition_stmt->execute();
                            $petition_id = $petition_stmt->fetchColumn();
                            $go_to_target_link = "Benevoles/view_activity.php?id=" . $petition_id;
                        }

                        //$go_to_target_link = "view_petition.php?id=" . $petition_id;
                    }

                    echo "<tr>";
                    echo "<td class='id'>".$type."</td>";
                    echo "<td class='content'><a href='/Sources/view_profile.php?id=". $user_id ."' target='blank_'>".$username."</a></td>";
                    echo "<td class='content'>".$target."</td>";
                    echo "<td class='actions'>";
                    if($type){
                        echo "<a href='/Sources/". $go_to_target_link ."' class='action' target='blank_'><img src='../../Resources/img/ui_icons/eye.png' alt='Modify'></a>";
                        echo "<a href='' class='void'>&nbsp;</a>";
                    }
                    echo "<a href='Processus/delete_report.php?id=" . htmlspecialchars($new['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>"; 
                    
                    if($new['current_status'] == 'OPEN' && ($target != "Cible supprimée") && ($is_banned <= 0)){
                        echo "<a href='' class='void'>&nbsp;</a>";
                        echo "<a href='" . $action_link . "' class='action' title='". $target_title ."'><img src='../../Resources/img/ui_icons/targets.png' alt='Delete'></a>";  
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
</div>
</div>


<?php include_once "footer.php"; ?>