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
            <tr>
                <td class="id">0</td>
                <td class="content">Quelle est la capitale du Zimbabwe ?</td>
                <td class="content">Harare</td>
                <td class="actions">
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/crayon.png" alt=""></a>
                    <a href="" class="void">&nbsp;</a>
                    <a href="" class="action"><img src="../../Resources/img/ui_icons/trash.png" alt=""></a>
                </td>
            </tr>
            <tr>
                <td class="id">1</td>
                <td class="content">Combien font 2 + 7 ?</td>
                <td class="content">9</td>
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
