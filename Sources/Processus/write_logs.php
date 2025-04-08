<?php

function write_logs($log_file, $type, $message) {
    if (is_writable($log_file)) {
        $handle = fopen($log_file, 'a');
        if ($handle) {
            fwrite($handle, date('d/m/Y H:i') . " UTC - " . $type . " " . $message . "\n");
            fclose($handle);
        } else {
            echo "Could not open the log file for writing.";
        }
    } else {
        echo "The log file is not writable.";
    }
}

?>