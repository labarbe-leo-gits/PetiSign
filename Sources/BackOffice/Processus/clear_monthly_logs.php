<?php
$logFile = "/var/www/html/Sources/logs/log.txt";

$backup_dir = "/var/www/html/Sources/logs/archives/";
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$date = date('Y-m');
$backup_file = $backup_dir . "log_$date.txt";

if (file_exists($logFile) && filesize($logFile) > 0) {
    copy($logFile, $backup_file);
    file_put_contents($logFile, date('d/m/Y H:i') . " UTC - [SYSTEM] - CronTab - 0.0.0.0 - Suppression auto. des logs ");
    $message = date('d/m/Y H:i') . " UTC - [SYSTEM] - CronTab - 0.0.0.0 - Backup des logs créé";
    file_put_contents($logFile, $message, FILE_APPEND);
}
?>