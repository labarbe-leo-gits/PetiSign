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
    $new_account_count = 0;
    $discover_count = 0;
    $profile_count = 0;
    $mysign_count = 0;
    $mypet_count = 0;
    $msg_count = 0;
    $index_count = 0;

    function countKeywordOccurrences($logLines, $keyword) {
        $count = 0;
        foreach ($logLines as $line) {
            if (strpos($line, $keyword) !== false) {
                $count++;
            }
        }
        return $count;
    }

    function countUniqueIPs($logLines, $keyword, &$uniqueIPs) {
        $count = 0;
        foreach ($logLines as $line) {
            if (strpos($line, $keyword) !== false) {
                if (preg_match('/\b(?:\d{1,3}\.){3}\d{1,3}\b/', $line, $matches)) {
                    $ip = $matches[0];
                    if (!in_array($ip, $uniqueIPs)) {
                        $uniqueIPs[] = $ip;
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    if (file_exists($logFile)) {
        $logLines = file($logFile);
        $uniqueIPs = [];

        $successfulConnections = countKeywordOccurrences($logLines, 'AUTH01');
        $new_account_count = countKeywordOccurrences($logLines, 'AUTH03');
        $discover_count = countUniqueIPs($logLines, 'D1SC0V', $uniqueIPs);
        $profile_count = countUniqueIPs($logLines, 'PROF1L', $uniqueIPs);
        $mysign_count = countUniqueIPs($logLines, 'MYS1GN', $uniqueIPs);
        $mypet_count = countUniqueIPs($logLines, 'MYP3TS', $uniqueIPs);
        $msg_count = countUniqueIPs($logLines, 'MSG1NG', $uniqueIPs);
        $index_count = countUniqueIPs($logLines, 'AC3UIL', $uniqueIPs);
    }
    ?>
    <div class="stat_container">
        <h3>Statistiques (30 derniers jours)</h3>
        <div class="main">
            <div class="stat">
                <p>Nombre de connexions : <strong id="connection-count"><?php echo $successfulConnections; ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouveau comptes : <strong id="new-account-count"><?php echo $new_account_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouvelles pétitions : <strong id="new-pet-count"><?php echo "0" ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouvelles signatures : <strong id="new-sign-count"><?php echo "0" ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouveaux commentaires : <strong id="new-coms-count"><?php echo "0" ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouveaux messages : <strong id="new-msg-count"><?php echo "0" ?></strong></p>
            </div>
            <div class="stat">
                <p>Nombre de nouveaux signalements : <strong id="new-report-count"><?php echo "0" ?></strong></p>
            </div>
        </div>
        <hr class="stat_hr">
        <h3>Visites uniques par page (30 derniers jours)</h3>
        <div class="main">
            <div class="stat">
                <p>'Accueil' : <strong id="index-count"><?php echo $index_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>'Découvrir' : <strong id="discover-count"><?php echo $discover_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>'Profil' : <strong id="profile-count"><?php echo $profile_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>'Mes Signatures' : <strong id="mysign-count"><?php echo $mysign_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>'Mes Pétitions' : <strong id="mypet-count"><?php echo $mypet_count; ?></strong></p>
            </div>
            <div class="stat">
                <p>'Messagerie' : <strong id="msg-count"><?php echo $msg_count; ?></strong></p>
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

<script src="/Sources/js/trancho_popup.js"></script>

<script>
    setInterval(() => {
        fetch('fetch_logs.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('logs_div').innerHTML = data;
                updateCount('AUTH01', 'connection-count');
                updateCount('AUTH03', 'new-account-count');
                updateUniqueCount('D1SC0V', 'discover-count');
                updateUniqueCount('PROF1L', 'profile-count');
                updateUniqueCount('MYS1GN', 'mysign-count');
                updateUniqueCount('MYP3TS', 'mypet-count');
                updateUniqueCount('MSG1NG', 'msg-count');
                updateUniqueCount('AC3UIL', 'index-count');
            })
            .catch(error => console.error('Error fetching logs:', error));
    }, 1000);

    function updateCount(keyword, elementId) {
        let logContent = document.getElementById('logs_div').innerHTML;
        let count = (logContent.match(new RegExp(keyword, 'g')) || []).length;
        document.getElementById(elementId).innerHTML = count;
    }

    function updateUniqueCount(keyword, elementId) {
        let logContent = document.getElementById('logs_div').innerHTML;
        let lines = logContent.split('<br>');
        let uniqueIPs = new Set();

        lines.forEach(line => {
            if (line.includes(keyword)) {
                let ipMatch = line.match(/\b(?:\d{1,3}\.){3}\d{1,3}\b/);
                if (ipMatch) {
                    uniqueIPs.add(ipMatch[0]);
                }
            }
        });

        document.getElementById(elementId).innerHTML = uniqueIPs.size;
    }
</script>

<?php
include_once 'footer.php';
?>