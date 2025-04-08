<?php

function write_logs($log_file, $type, $user, $ip, $action) {
    $date = date('d/m/Y H:i');
    $date = $date . " UTC ";
    $log_line = "$date - [$type] - $user - $ip - $action" . PHP_EOL;

    if (is_writable($log_file)) {
        $handle = fopen($log_file, 'a');
        if ($handle) {
            fwrite($handle, $log_line);
            fclose($handle);
        } else {
            echo "Impossible d'ouvrir pour ecrire.";
        }
    } else {
        echo "Les log ne sont pas accessibles en écriture.";
    }
}
?>
