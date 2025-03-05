<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">



<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Gestion des équipes Bénévoles</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="window.location.reload(true);"><img src="../../Resources/img/ui_icons/refresh.png" alt="Actualiser la page"> Actualiser</a>
        <a class="captcha_database_action" href="add_team.php"><img src="../../Resources/img/ui_icons/plus.png" alt="Nouveau Captcha"> Nouvelle équipe</a>
    </div>
    <div class="tableau">
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Secteur</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td class="id">0</td>
                <td class="content">Les Voix Citoyennes</td>
                <td class="content">Paris 11°</td>
                <td class="actions">
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/eye.png" alt="Visualiser"></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/crayon.png" alt=""></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/trash.png" alt=""></a>
                </td>
            </tr>
            <tr>
                <td class="id">1</td>
                <td class="content">Action Signature</td>
                <td class="content">Saint-Denis</td>
                <td class="actions">
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/eye.png" alt="Visualiser"></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/crayon.png" alt=""></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/trash.png" alt=""></a>
                </td>
            </tr>
        </table>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>
