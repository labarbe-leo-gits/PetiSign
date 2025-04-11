<?php

include_once '../database/database.php';

$report_type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_NUMBER_INT);
$target_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);


if ($report_type === null || $target_id === null || $report_type === false || $target_id === false || $report_type === '' || $target_id === '' || empty($report_type) || empty($target_id) || !$report_type || !$target_id) {
    header('Location: ../index.php');
    exit;
}

if ($report_type < 1 || $report_type > 3) {
    header('Location: ../index.php');
    exit;
}

$get_reporter_id = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_reporter_id->bindParam(':mail', $_SESSION['mail']);
$get_reporter_id->execute();
$reporter_id = $get_reporter_id->fetchColumn();

// 1 : User
// 2 : Petitions
// 3 : Comments

if ($report_type == 1) {
    echo "Report type 1 selected.";
} elseif ($report_type == 2) {
    
    $create_report_stmt = $pdo->prepare('INSERT INTO REPORT (id_user, id_target, report_type, reason) VALUES (:id_user, :id_target, :report_type, :reason)');
    $create_report_stmt->bindParam(':id_user', $reporter_id);
    $create_report_stmt->bindParam(':id_target', $target_id);
    $create_report_stmt->bindParam(':report_type', $report_type);
    $create_report_stmt->bindParam(':reason', $report_reason);

} elseif ($report_type == 3) {
    echo "Report type 3 selected.";
} else {
    header('Location: ../index.php');
    exit;
}

?>