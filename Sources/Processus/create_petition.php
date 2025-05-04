<?php

include_once '../loading.php';
include_once '../database/database.php';
include_once 'write_logs.php';

session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $petition_name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $petition_category = htmlspecialchars(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));
    $petition_description = htmlspecialchars(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $petition_goal = htmlspecialchars(filter_input(INPUT_POST, 'objectif', FILTER_SANITIZE_NUMBER_INT));
    $petition_img = htmlspecialchars(filter_input(INPUT_POST, 'img_id', FILTER_SANITIZE_NUMBER_INT));
    $user_id = htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT));

    $petition_name_length = strlen($petition_name);
    $petition_description_length = strlen($petition_description);

    if ($petition_name_length > 60) {
        echo "Petition name is too long";
        exit();
    }

    if ($petition_description_length > 800) {
        echo "Petition description is too long";
        exit();
    }

    if (empty($petition_name)) {
        echo "Petition name is empty";
        exit();
    }
    if (empty($petition_category)) {
        echo "Petition category is empty";
        exit();
    }
    if (empty($petition_description)) {
        echo "Petition description is empty";
        exit();
    }
    if (empty($petition_goal)) {
        echo "Petition goal is empty";
        exit();
    }
    if (empty($petition_img)) {
        echo "Petition image ID is empty";
        exit();
    }
    if (empty($user_id)) {
        echo "User ID is empty";
        exit();
    }

    if (!$petition_name || !$petition_category || !$petition_description || !$petition_goal || !$petition_img || !$user_id) {
        echo "Invalid input detected";
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

        header('Location: ../view_petition.php?id=' . $petition_id);
        exit();
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit();
    }
} else {
    header('Location: ../create_petition.php');
    exit();
}
?>