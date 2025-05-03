<?php
include_once '../loading.php';
session_start();
include_once '../database/database.php';

if(!isset($_SESSION['mail'])){
    header('Location: ../login.php');
    exit();
}

$com_id = htmlspecialchars(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));    

if($_SERVER["REQUEST_METHOD"] != "GET"){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if(!$com_id || !is_numeric($com_id) || empty($com_id)){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$check_if_id_exist_stmt = $pdo->prepare("SELECT id FROM COMMENT WHERE id = :id");
$check_if_id_exist_stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
$check_if_id_exist_stmt->execute();
$check_if_id_exist = $check_if_id_exist_stmt->fetchColumn();

if(!$check_if_id_exist){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$is_admin_stmt = $pdo->prepare("SELECT is_admin FROM USER WHERE email = :mail");
$is_admin_stmt->bindParam(':mail', $_SESSION['mail']);
$is_admin_stmt->execute();
$is_admin = $is_admin_stmt->fetchColumn();

if($is_admin != 0){

    try {
        $stmt = $pdo->prepare("DELETE FROM COMMENT WHERE id = :id");
        $stmt->bindParam(':id', $com_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: ". $_SERVER['HTTP_REFERER']);
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: ../error.php?code=403');
    exit();

}

?>