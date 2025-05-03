<?php
include_once '../loading.php';
session_start();
include_once '../database/database.php';

$pet_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);    

if($_SERVER["REQUEST_METHOD"] != "GET"){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

if(!$pet_id || !is_numeric($pet_id) || empty($pet_id)){
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$is_admin_stmt = $pdo->prepare("SELECT is_admin FROM USER WHERE email = :mail");
$is_admin_stmt->bindParam(':mail', $_SESSION['mail']);
$is_admin_stmt->execute();
$is_admin = $is_admin_stmt->fetchColumn();

if($is_admin != 0){

    try {

        $delete_signature_stmt = $pdo->prepare("DELETE FROM SIGNATURE WHERE id_petition = :id");
        $delete_signature_stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
        $delete_signature_stmt->execute();

        $delete_comment_stmt = $pdo->prepare("DELETE FROM COMMENT WHERE id_petition = :id");
        $delete_comment_stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
        $delete_comment_stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM PETITION WHERE id = :id");
        $stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: ../discover.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header('Location: ../error.php?code=403');
    exit();

}

?>