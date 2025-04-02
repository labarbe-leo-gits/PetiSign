<?php

$logFile = "../logs/log.txt";
if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    echo nl2br($logContent);
} else {
    echo "Log file not found.";
}
?>