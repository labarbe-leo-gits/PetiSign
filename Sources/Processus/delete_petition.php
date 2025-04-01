<?php
include_once '../database/database.php';

session_start();
if(!isset($_SESSION['mail'])){
    header('Location: ../login.php');
    exit();
}

if(!isset($_GET['id'])){
    header('Location: ../my_petitions.php');
    exit();
}

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$user_id = $get_user_id_stmt->fetchColumn();

$get_user_petitions_id = $pdo->prepare("SELECT id FROM PETITION WHERE user = :user_id");
$get_user_petitions_id->bindParam(':user_id', $user_id);
$get_user_petitions_id->execute();
$petitions_id = $get_user_petitions_id->fetchAll();

$pet_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$clean_array = array();
foreach ($petitions_id as $key => $value) {
    $clean_array[] = $value['id'];
}

if(!in_array($pet_id, $clean_array)){
    header('Location: ../my_petitions.php');
    exit();
}

try {

    $stmt = $pdo->prepare('DELETE FROM SIGNATURE WHERE id_petition = :id');
    $stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare('DELETE FROM COMMENT WHERE id_petition = :id');
    $stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare('DELETE FROM PETITION WHERE id = :id');
    $stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: ../my_petitions.php');
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>