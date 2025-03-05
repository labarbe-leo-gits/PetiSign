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
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
            <tr>
                <td class="id">0</td>
                <td class="content">SuperKiwi</td>
                <td class="content">Utilisateur</td>
                <td class="actions">
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/crayon.png" alt=""></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/trash.png" alt=""></a>
                </td>
            </tr>
            <tr>
                <td class="id">1</td>
                <td class="content">FDupont</td>
                <td class="content">Bénévole</td>
                <td class="actions">
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/crayon.png" alt=""></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/trash.png" alt=""></a>
                </td>
            </tr>
            <tr>
                <td class="id">2</td>
                <td class="content">DetrauxL</td>
                <td class="content">Administrateur</td>
                <td class="actions">
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
