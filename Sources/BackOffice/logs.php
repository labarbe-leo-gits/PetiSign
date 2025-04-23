<?php
include_once 'header.php';
?>

<link rel="stylesheet" href="../css/backoffice_tablepages.css">
<link rel="stylesheet" href="../css/backoffice_logs.css">

<div class="right_panel">
    <div class="title">
        <h2 class="highlighted-text" id="page_title">Logs</h2>
    </div>
    <div class="database_actions_container">
        <a class="captcha_database_action" onclick="show_popup_trancho()"><img src="../../Resources/img/ui_icons/download.png">&nbsp;&nbsp;Télécharger</a>
        <a class="captcha_database_action" onclick="window.location.href = 'Processus/logs_deletion.php'"><img src="../../Resources/img/ui_icons/trash.png" alt="Actualiser la page">&nbsp;&nbsp;Supprimer</a>
    </div>
    <div class="logs_container">
        <div class="logs" id="logs_div">
            <?php
            $logFile = "../logs/log.txt";
            if (file_exists($logFile)) {
                $logContent = file_get_contents($logFile);
                echo nl2br($logContent);
            } else {
                echo "Log file not found.";
            }
            
            ?>
        </div>
    </div>
    <?php

    $successfulConnections = 0;
    $discover_count = 0;
    if (file_exists($logFile)) {
        $logLines = file($logFile);
        foreach ($logLines as $line) {
            if (strpos($line, 'Connexion réussie') !== false) {
                $successfulConnections++;
            }
            if (strpos($line, 'Découvrir') !== false) {
                $discover_count++;
            }
        }
    }


    ?>
    <div class="stat_container">
        <div class="stat">
            <p>Connexions : <strong id="connection-count"><?php echo $successfulConnections; ?></strong></p>
        </div>
        <hr>
        <div class="stat">
            <p>Découvrir : <strong id="discover-count"><?php echo $discover_count; ?></strong></p>
        </div>
    </div>
</div>
</div>

<link rel="stylesheet" href="/Sources/css/sign_popup.css">
<div class="filter">&nbsp;</div>

    <div class="container_ popup">
        <div class="close"><img onclick="hide_popup_trancho()" src="/Resources/img/ui_icons/plus.png" alt="Fermer la Popup"></div>
        <div class="right">
            <form action="Processus/download.php" method="post">
                <input type="hidden" name="petition_id" value="<?=$_GET['id']?>">
                <h1>Téléchargement</h1>
                <hr>
                <p>En téléchargeant le contenu des logs, vous vous engagez, en tant qu'administrateur, à ne pas diffuser ces données. En cas de problème lié à ce téléchargement, votre responsabilité exclusive sera engagée.</p>
                <input type="checkbox" name="check2" id="check2" required>
                <label for="check2">J'affirme avoir pris connaissance des éléments mentionnés ci-dessus</label>
                <hr class="bottom_hr">
                <div class="send"><button class="button_" type="submit">Télécharger</button></div>
            </form>
        </div>
    </div>
</div>

<script src="/Sources/js/trancho_popup.js"></script>

<script>
    setInterval(() => {
        fetch('fetch_logs.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('logs_div').innerHTML = data;
                updateConnectionCount();
                discoverCount();
            })
            .catch(error => console.error('Error fetching logs:', error));
    }, 1000);
    

    function updateConnectionCount() {
        let logContent = document.getElementById('logs_div').innerHTML;
        let count = (logContent.match(/Connexion réussie/g) || []).length;
        document.getElementById('connection-count').innerHTML = count;
    }

    function discoverCount() {
        let logContent = document.getElementById('logs_div').innerHTML;
        let count = (logContent.match(/Découvrir/g) || []).length;
        document.getElementById('discover-count').innerHTML = count;
    }

</script>

<?php
include_once 'footer.php';
?>