<?php

function write_logs($log_file, $type, $message) {
    if (is_writable($log_file)) {
        $handle = fopen($log_file, 'a');
        if ($handle) {
            fwrite($handle, date('Y-m-d H:i:s') . " - " . $type . " " . $message . "\n");
            fclose($handle);
        } else {
            echo "Could not open the log file for writing.";
        }
    } else {
        echo "The log file is not writable.";
    }
}

?>