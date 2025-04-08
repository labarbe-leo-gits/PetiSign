<?php

include_once '../database/database.php';

$report_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT);
$target_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($report_type === null || $target_id === null || empty($report_type) || empty($target_id)) {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit;
}


if ($report_type == 1) {
    
    // user

} elseif ($report_type == 2) {
    
    // petition
    echo "fn";

} elseif ($report_type == 3) {
    
    // commentaire

} else {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit;
}


?>