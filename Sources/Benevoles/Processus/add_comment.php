<?php
include_once '../../loading.php';
session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

include_once 'security.php';

if($id_benevole != 1) {
    header('Location: /Sources/error.php?error=403');
    exit();
}

include_once '/Sources/database/database.php';

try{
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
    $activity_id = filter_input(INPUT_POST, 'activity_id', FILTER_SANITIZE_NUMBER_INT);
    $comment_length = mb_strlen($comment);
    if($comment_length > 200) {
        header('Location: ../view_activity.php?id=' . $activity_id . '&error=comment_too_long');
        exit();
    }

}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

if(empty($comment) || !is_numeric($user_id) || !is_numeric($activity_id) || !$comment) {
    header('Location: '. $_SERVER['HTTP_REFERER']);
    exit();
}	

try{

    $stmt = $pdo->prepare("INSERT INTO COMMENT (id_user, id_target, content, target_type) VALUES (:user_id, :petition_id, :comment, 2)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':petition_id', $activity_id);
    $stmt->bindParam(':comment', $comment);
    $stmt->execute();

    echo "Comment added successfully!";
    header('Location: ../view_activity.php?id=' . $activity_id);

}catch(PDOException $e){
    echo "Error: " . $e->getMessage();
    exit();
}

?>