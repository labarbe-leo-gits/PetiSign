<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion du Captcha</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
        <a class="captcha_database_action" href="add_captcha.php"><img src="../../Resources/img/ui_icons/plus.png" alt="Nouveau Captcha"> Nouveau Captcha</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Réponse</th>
                <th>Actions</th>
            </tr>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM CAPTCHA");
                $stmt->execute();
                $captchas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($captchas as $captcha){
                    echo "<tr>";
                    echo "<td class='id'>".$captcha['id']."</td>";
                    echo "<td class='content'>".$captcha['question']."</td>";
                    echo "<td class='content'>".$captcha['answer']."</td>";
                    echo "<td class='actions'>";

                    if($captcha['state'] == 1){
                        echo "<a href='Processus/disable_captcha.php?id=" . htmlspecialchars($captcha['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/cross.png' alt='Disable'></a>";
                    } else {
                        echo "<a href='Processus/enable_captcha.php?id=" . htmlspecialchars($captcha['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/validate.png' alt='Enable'></a>";
                    }
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='modify_captcha_form.php?id=" . htmlspecialchars($captcha['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/crayon.png' alt='Modify'></a>";
                    echo "<a href='' class='void'>&nbsp;</a>";
                    echo "<a href='Processus/delete_captcha.php?id=" . htmlspecialchars($captcha['id'], ENT_QUOTES, 'UTF-8') . "' class='action'><img src='../../Resources/img/ui_icons/trash.png' alt='Delete'></a>";
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
