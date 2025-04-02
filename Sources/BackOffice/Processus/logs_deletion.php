<?php
include_once '../../database/database.php';
include_once 'security.php';

if($id_admin != 0){

    $logFile = '../../logs/log.txt';
    if (file_exists($logFile)) {
        if (is_writable($logFile)) {
            file_put_contents($logFile, '');
            header('Location: '. $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: '. $_SERVER['HTTP_REFERER']);
        }
    } else {
        header('Location: '. $_SERVER['HTTP_REFERER']);
    }

}
else {
    header('Location: /Sources/error.php?code=403');
    exit();
}

?>