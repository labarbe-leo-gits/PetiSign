<?php
include_once '../loading.php';
include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_POST['sender'];
    $message = $_POST['message'];
    $discussion_id = $_POST['discussion_id'];

    $stmt = $pdo->prepare("INSERT INTO MESSAGE (content, sender, id_discussion) VALUES (:content, :sender, :id_discussion)");
    $stmt->bindParam(':content', $message);
    $stmt->bindParam(':sender', $user_id);
    $stmt->bindParam(':id_discussion', $discussion_id);
    $stmt->execute();

    //header('Location: ../chat.php?discussion_id=' . $discussion_id);
    echo "<script>window.location.href = '../chat.php?discussion_id=' . $discussion_id;</script>";
    
} else {
    //header('Location: '. $_SERVER['HTTP_REFERER']);
    echo "<script>window.location.href = '" . $_SERVER['HTTP_REFERER'] . "';</script>";
    exit;
}

?>