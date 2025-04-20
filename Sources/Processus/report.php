<?php

session_start();

if (!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

include_once '../database/database.php';


$get_id_from_mail_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_id_from_mail_stmt->bindValue(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_id_from_mail_stmt->execute();
$user_id = $get_id_from_mail_stmt->fetchColumn();

$report_type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT);
$target_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
//$report_reason = htmlspecialchars(filter_input(INPUT_GET, 'reason', FILTER_SANITIZE_STRING));

// 1 : User
// 2 : Petitions
// 3 : Comments

if($report_type === false || $target_id === false || empty($report_type) || empty($target_id) || !$report_type || !$target_id){
    echo "Invalid input.";
    exit();
}

if(!in_array($report_type, [1, 2, 3])) {
    echo "Invalid report type.";
    exit();
}

if($report_type == 1){

    $check_if_user_exists_stmt = $pdo->prepare('SELECT COUNT(*) FROM USER WHERE id = :target_id');
    $check_if_user_exists_stmt->bindValue(':target_id', $target_id, PDO::PARAM_INT);
    $check_if_user_exists_stmt->execute();
    $user_exists = $check_if_user_exists_stmt->fetchColumn();

    if($user_exists == 0){
        echo "User does not exist.";
        exit();
    }

}
else if($report_type == 2){

    $check_if_petition_exists_stmt = $pdo->prepare('SELECT COUNT(*) FROM PETITION WHERE id = :target_id');
    $check_if_petition_exists_stmt->bindValue(':target_id', $target_id, PDO::PARAM_INT);
    $check_if_petition_exists_stmt->execute();
    $petition_exists = $check_if_petition_exists_stmt->fetchColumn();

    if($petition_exists == 0){
        echo "Petition does not exist.";
        exit();
    }

}
else if($report_type == 3){

    $check_if_comment_exists_stmt = $pdo->prepare('SELECT COUNT(*) FROM COMMENT WHERE id = :target_id');
    $check_if_comment_exists_stmt->bindValue(':target_id', $target_id, PDO::PARAM_INT);
    $check_if_comment_exists_stmt->execute();
    $comment_exists = $check_if_comment_exists_stmt->fetchColumn();

    if($comment_exists == 0){
        echo "Comment does not exist.";
        exit();
    }

}

$check_if_already_reported_stmt = $pdo->prepare('SELECT COUNT(*) FROM REPORT WHERE id_target = :target_id AND id_user = :user_id AND report_type = :report_type');
$check_if_already_reported_stmt->bindValue(':target_id', $target_id, PDO::PARAM_INT);
$check_if_already_reported_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$check_if_already_reported_stmt->bindValue(':report_type', $report_type, PDO::PARAM_INT);
$check_if_already_reported_stmt->execute();
$already_reported = $check_if_already_reported_stmt->fetchColumn();

if($already_reported > 0){
    echo "Repot already exists.";
    exit();
}

try{
    $create_report_stmt = $pdo->prepare('INSERT INTO REPORT (report_type, id_target, id_user, reason) VALUES (:report_type, :target_id, :user_id, :report_reason)');
    $create_report_stmt->bindValue(':report_type', $report_type, PDO::PARAM_INT);
    $create_report_stmt->bindValue(':target_id', $target_id, PDO::PARAM_INT);
    $create_report_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $create_report_stmt->bindValue(':report_reason', $report_reason, PDO::PARAM_STR);
    $create_report_stmt->execute();
} catch (PDOException $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
    exit();
}

echo "success";

?>