<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion des utilisateurs</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
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
                    echo "<tr>";
                    echo "<td class='id'>".$user['id']."</td>";
                    echo "<td class='content'>".$user['username']."</td>";
                    if($user['is_admin'] == 1){
                        if($user['is_benevole'] == 1){
                            echo "<td class='content'>Administrateur, Bénévole</td>";
                        } else {
                            echo "<td class='content'>Administrateur</td>";
                        }
                    } else if($user['is_benevole'] == 1){
                        echo "<td class='content'>Bénévole</td>";
                    } else {
                        echo "<td class='content'>Utilisateur</td>";
                    }
                    echo "<td class='actions'>";
                    echo "<a href='modify_user_form.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/crayon.png' alt='Modify'></a>";
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='Processus/delete_user.php?id=" . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
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
