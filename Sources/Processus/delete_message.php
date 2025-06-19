<?php
include_once '../loading.php';
session_start();
include_once '../database/database.php';

if(!isset($_SESSION['mail'])){
    //header('Location: ../login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

$com_id = htmlspecialchars(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));    

if($_SERVER["REQUEST_METHOD"] != "GET"){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."';</script>";
    exit();
}

if(!$com_id || !is_numeric($com_id) || empty($com_id)){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."';</script>";
    exit();
}

$check_if_id_exist_stmt = $pdo->prepare("SELECT id FROM MESSAGE WHERE id = :id");
$check_if_id_exist_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$check_if_id_exist_stmt->execute();
$check_if_id_exist = $check_if_id_exist_stmt->fetchColumn();

if(!$check_if_id_exist){
    /* header('Location: ' . $_SERVER['HTTP_REFERER']); */
    echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."';</script>";
    exit();
}

$get_logged_user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_logged_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_logged_user_id_stmt->execute();
$logged_user_id = $get_logged_user_id_stmt->fetchColumn();

$get_comment_owner = $pdo->prepare("SELECT sender FROM MESSAGE WHERE id = :id");
$get_comment_owner->bindParam(':id', $com_id, PDO::PARAM_INT);
$get_comment_owner->execute();
$comment_owner = $get_comment_owner->fetchColumn();

if($logged_user_id == $comment_owner){

    try {

        $stmt = $pdo->prepare("DELETE FROM MESSAGE WHERE id = :id");
        $stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
        $stmt->execute();

        //header("Location: ". $_SERVER['HTTP_REFERER']);
        echo "<script>window.location.href = '". $_SERVER['HTTP_REFERER'] ."';</script>";
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // header('Location: ../error.php?code=403');
    echo "<script>window.location.href = '../error.php?code=403';</script>";
    exit();

}

?>