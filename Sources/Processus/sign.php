<?php

try{

    session_start();

    if (!isset($_SESSION['mail'])) {
        header('Location: ../login.php');
        exit();
    }

    include_once '../database/database.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $petition_id = htmlspecialchars(filter_input(INPUT_POST, 'petition_id', FILTER_SANITIZE_NUMBER_INT));

        $user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $user_id_stmt->bindParam(':mail', $_SESSION['mail']);
        $user_id_stmt->execute();
        $user_id = $user_id_stmt->fetchColumn();

        $petition_goal_stmt = $pdo->prepare("SELECT signature_goal FROM PETITION WHERE id = :id");
        $petition_goal_stmt->bindParam(':id', $petition_id);
        $petition_goal_stmt->execute();
        $petition_goal = $petition_goal_stmt->fetchColumn();

        $signature_stmt = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_user = :user_id AND id_petition = :petition_id");
        $signature_stmt->bindParam(':user_id', $user_id);
        $signature_stmt->bindParam(':petition_id', $petition_id);
        $signature_stmt->execute();
        $signature_count = $signature_stmt->fetchColumn();

        if ($signature_count > 0) {
            header('Location: '. $_SERVER['HTTP_REFERER']);
            exit();
        }

        $check_goal_stmt = $pdo->prepare("SELECT signature_count FROM PETITION WHERE id = :id");
        $check_goal_stmt->bindParam(':id', $petition_id);
        $check_goal_stmt->execute();
        $check_goal = $check_goal_stmt->fetchColumn();

        if($check_goal >= $petition_goal) {
            echo "Error: The petition has already reached its goal.";
            exit();
        }

        $petition_signature_stmt = $pdo->prepare("SELECT COUNT(*) FROM SIGNATURE WHERE id_petition = :id");
        $petition_signature_stmt->bindParam(':id', $petition_id);
        $petition_signature_stmt->execute();
        $petition_signature = $petition_signature_stmt->fetchColumn();

        $insert_stmt = $pdo->prepare("INSERT INTO SIGNATURE (id_user, id_petition) VALUES (:user_id, :petition_id)");
        $insert_stmt->bindParam(':user_id', $user_id);
        $insert_stmt->bindParam(':petition_id', $petition_id);
        $insert_stmt->execute();

        $update_stmt = $pdo->prepare("UPDATE PETITION SET signature_count = signature_count + 1 WHERE id = :id");
        $update_stmt->bindParam(':id', $petition_id);
        $update_stmt->execute();
        
        $update_stmt2 = $pdo->prepare("UPDATE PETITION SET statut = 'CLOSED' WHERE id = :id AND signature_goal <= :signature_goal");
        $update_stmt2->bindParam(':id', $petition_id);
        $update_stmt2->bindParam(':signature_goal', $petition_signature);
        $update_stmt2->execute();


    } else {
        header('Location: ../index.php');
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>