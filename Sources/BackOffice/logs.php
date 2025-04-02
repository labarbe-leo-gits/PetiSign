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
        <a class="captcha_database_action" onclick="window.location.href = 'Processus/logs_deletion.php'"><img src="../../Resources/img/ui_icons/trash.png" alt="Actualiser la page">&nbsp;&nbsp;Supprimer les Logs</a>
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
</div>

<script>
    setInterval(() => {
        fetch('fetch_logs.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('logs_div').innerHTML = data;
            })
            .catch(error => console.error('Error fetching logs:', error));
    }, 1000);
</script>

<?php
include_once 'footer.php';
?>
