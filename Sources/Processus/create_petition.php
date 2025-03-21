<?php
include_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $petition_name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $petition_category = htmlspecialchars(filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING));
    $petition_description = htmlspecialchars(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));
    $petition_goal = htmlspecialchars(filter_input(INPUT_POST, 'objectif', FILTER_SANITIZE_NUMBER_INT));
    $petition_img = htmlspecialchars(filter_input(INPUT_POST, 'img_id', FILTER_SANITIZE_NUMBER_INT));
    $user_id = htmlspecialchars(filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT));

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

    try {
        $insert_stmt = $pdo->prepare("INSERT INTO PETITION (title, category, description, signature_goal, image_id, user) VALUES (:name, :category, :description, :signature_goal, :image_id, :user_id)");
        $insert_stmt->bindParam(':name', $petition_name);
        $insert_stmt->bindParam(':category', $petition_category);
        $insert_stmt->bindParam(':description', $petition_description);
        $insert_stmt->bindParam(':signature_goal', $petition_goal);
        $insert_stmt->bindParam(':image_id', $petition_img);
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->execute();

        header('Location: ../index.php');
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