<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion de la Base de Données</h2>
    </div>
    <div class="title">
        <h3 class="highlighted-text" id="page_title">Catégories</h3>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" href="add_captcha.php"><img src="../../Resources/img/ui_icons/plus.png" alt="Nouveau Captcha"> Nouvelle Catégorie</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
            <?php
            try{
                $stmt = $pdo->prepare("SELECT * FROM CATEGORY");
                $stmt->execute();
                $captchas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($captchas as $captcha){
                    echo "<tr>";
                    echo "<td class='id'>".$captcha['id']."</td>";
                    echo "<td class='content'>".$captcha['name']."</td>";
                    echo "<td class='actions'>";
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
