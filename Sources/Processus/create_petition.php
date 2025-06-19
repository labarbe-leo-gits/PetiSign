<?php

include_once '../loading.php';
include_once '../database/database.php';
include_once 'write_logs.php';

session_start();

if(!isset($_SESSION['mail'])) {
    //header('Location: ../login.php');
    echo "<script>window.location.href = '../login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $petition_name_unfiltered = $_POST['name'] ?? '';
    $petition_name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $petition_category = htmlspecialchars(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));
    $petition_description = htmlspecialchars(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $petition_description_unfiltered = $_POST['description'] ?? '';
    $petition_goal = htmlspecialchars(filter_input(INPUT_POST, 'objectif', FILTER_SANITIZE_NUMBER_INT));
    $petition_img = htmlspecialchars(filter_input(INPUT_POST, 'img_id', FILTER_SANITIZE_NUMBER_INT));
    $user_id = htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT));

    $petition_name_length = mb_strlen($petition_name_unfiltered);
    $petition_description_length = mb_strlen($petition_description_unfiltered);

    $petition_name = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $petition_name_unfiltered);
$petition_description = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $petition_description_unfiltered);

    if ($petition_name_length > 60) {
        echo "Petition name is too long";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }

    if ($petition_description_length > 800) {
        echo "Petition description is too long";
        echo $petition_description_length;
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }

    if (empty($petition_name)) {
        echo "Petition name is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }
    if (empty($petition_category)) {
        echo "Petition category is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }
    if (empty($petition_description)) {
        echo "Petition description is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }
    if (empty($petition_goal)) {
        echo "Petition goal is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }
    if (empty($petition_img)) {
        echo "Petition image ID is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }
    if (empty($user_id)) {
        echo "User ID is empty";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }

    if (!$petition_name || !$petition_category || !$petition_description || !$petition_goal || !$petition_img || !$user_id) {
        echo "Invalid input detected";
        echo "<script>window.location.href = '../create_petition.php';</script>";
        exit();
    }

    $petition_stage_one = ($petition_goal / 4) * 1;
    $petition_stage_two = ($petition_goal / 4) * 2;
    $petition_stage_three = ($petition_goal / 4) * 3;
    $petition_stage_four = $petition_goal;

    try {
        $insert_stmt = $pdo->prepare("INSERT INTO PETITION (title, category, description, signature_goal, image_id, user, signature_stage_one, signature_stage_two, signature_stage_three, signature_stage_four) VALUES (:name, :category, :description, :signature_goal, :image_id, :user_id, :signature_stage_one, :signature_stage_two, :signature_stage_three, :signature_stage_four)");
        $insert_stmt->bindParam(':name', $petition_name);
        $insert_stmt->bindParam(':category', $petition_category);
        $insert_stmt->bindParam(':description', $petition_description);
        $insert_stmt->bindParam(':signature_goal', $petition_goal);
        $insert_stmt->bindParam(':image_id', $petition_img);
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->bindParam(':signature_stage_one', $petition_stage_one);
        $insert_stmt->bindParam(':signature_stage_two', $petition_stage_two);
        $insert_stmt->bindParam(':signature_stage_three', $petition_stage_three);
        $insert_stmt->bindParam(':signature_stage_four', $petition_stage_four);
        $insert_stmt->execute();

        $get_pet_id = $pdo->prepare("SELECT id FROM PETITION WHERE title = :pet_name AND user = :user_id");
        $get_pet_id ->bindParam(':pet_name', $petition_name);
        $get_pet_id->bindParam(':user_id', $user_id);
        $get_pet_id->execute();
        $petition_id = $get_pet_id->fetch(PDO::FETCH_ASSOC)['id'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $user = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $user->bindParam(':id', $user_id);
        $user->execute();
        $user = $user->fetchColumn();

        write_logs('../logs/log.txt', 'N3WP3T', $user, $ip, 'Nouvelle pétition créée');

        //header('Location: ../view_petition.php?id=' . $petition_id);
        echo "<script>window.location.href = '../view_petition.php?id=" . $petition_id . "';</script>";
        exit();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit();
    }
} else {
    //header('Location: ../create_petition.php');
    echo "<script>window.location.href = '../create_petition.php';</script>";
    exit();
}
?>