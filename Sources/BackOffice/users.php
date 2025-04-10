<?php
include_once 'header.php';

$get_current_admin_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_current_admin_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_current_admin_id->execute();
$current_admin_id = $get_current_admin_id->fetchColumn();

?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion des utilisateurs</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/plus.png" alt="Ajouter un admin"> Ajouter un compte administrateur</a>
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
            <tr>
                <td class="id">0</td>
                <td class="content">SuperKiwi</td>
            </tr>
            <tr>
                <td class="id">1</td>
                <td class="content">FDupont</td>
            </tr>
            <tr>
                <td class="id">2</td>
                <td class="content">DetrauxL</td>
            </tr>
        </table>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>
