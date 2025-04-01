<?php

session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

include_once '../database/database.php';

$user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
$petition_id = filter_input(INPUT_POST, 'petition_id', FILTER_SANITIZE_NUMBER_INT);

if(empty($comment) || !is_numeric($user_id) || !is_numeric($petition_id) || !$comment) {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit();
}	

try{

    $stmt = $pdo->prepare("INSERT INTO COMMENT (id_user, id_petition, content) VALUES (:user_id, :petition_id, :comment)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':petition_id', $petition_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->execute();

    echo "Comment added successfully!";
    header('Location: ../view_petition.php?id=' . $petition_id);

}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

?>