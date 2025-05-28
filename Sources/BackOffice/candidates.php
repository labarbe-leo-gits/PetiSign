<?php
include_once 'header.php';
include_once '../checker.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion des candidatures</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>Date</th>
                <th>Utilisateur</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM USER_CANDIDATE");
                $stmt->execute();
                $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($teams as $team){

                    $user_id_to_username_stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                    $user_id_to_username_stmt->bindParam(':id', $team['id_user'], PDO::PARAM_INT);
                    $user_id_to_username_stmt->execute();
                    $user = $user_id_to_username_stmt->fetchColumn();

                    $formated_date_timestamp = date('d/m/Y', strtotime($team['date']));

                    echo "<tr>";
                    echo "<td class='id'>".$formated_date_timestamp."</td>";
                    echo "<td class='content'><a href='/Sources/view_profile.php?id=".$team['id_user']."' target='_blank'>".$user."</a></td>";
                    echo "<td class='content'>".$team['current_status']."</td>";
                    echo "<td class='actions'>";

                    echo "<a href='view_candidature.php?id=" . htmlspecialchars($team['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/eye.png' alt='View'></a>";
                    if($team['current_status'] == 'En Attente'){
                        echo "<a href='' class='void'>&nbsp;</a>";
                        echo "<a href='Processus/accept_candidate.php?id=" . htmlspecialchars($team['id'], ENT_QUOTES, 'UTF-8') . "&user_id=".$team['id_user']."' class='action'><img src='../../Resources/img/ui_icons/validate.png' alt='Modify'></a>";
                    }
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='Processus/delete_candidate.php?id=" . htmlspecialchars($team['id'], ENT_QUOTES, 'UTF-8') . "&user_id=".$team['id_user']."' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
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

<?php
include_once 'footer.php';
?>
