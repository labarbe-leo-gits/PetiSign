<?php

include_once '../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

    if (!empty($id) && !empty($name)) {
        try {
            $stmt = $pdo->prepare("UPDATE CATEGORY SET name = :name WHERE id = :id");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: ../database_gestion.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../modify_category_form.php?id=$id");
        exit();
    }
} else {
    header("Location: ../database_gestion.php");
    exit();
}

?>