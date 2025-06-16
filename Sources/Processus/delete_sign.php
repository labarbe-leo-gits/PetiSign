<?php
include_once '../loading.php';
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

$get_user_signatures_stmt = $pdo->prepare('SELECT id_petition FROM SIGNATURE WHERE id_user = :id');
$get_user_signatures_stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$get_user_signatures_stmt->execute();
$petitions_id = $get_user_signatures_stmt->fetchAll(PDO::FETCH_ASSOC);

$pet_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$clean_array = array();
foreach ($petitions_id as $key => $value) {
    $clean_array[] = $value['id_petition'];
}

if(!in_array($pet_id, $clean_array)){
    header('Location: ../my_signatures.php');
    exit();
}

try {

    $get_file_name_of_signature_stmt = $pdo->prepare('SELECT mobile_signature_filename FROM SIGNATURE WHERE id_petition = :id AND id_user = :user_id');
    $get_file_name_of_signature_stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $get_file_name_of_signature_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $get_file_name_of_signature_stmt->execute();
    $file_name = $get_file_name_of_signature_stmt->fetchColumn();

    if ($file_name) {
        $file_path = '../../Resources/signatures/' . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM SIGNATURE WHERE id_petition = :id AND id_user = :user_id');
    $stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $update_pet_signature_count_stmt = $pdo->prepare('UPDATE PETITION SET signature_count = signature_count - 1 WHERE id = :id');
    $update_pet_signature_count_stmt->bindParam(':id', $pet_id, PDO::PARAM_INT);
    $update_pet_signature_count_stmt->execute();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>