<?php
include_once '../../database/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM CATEGORY WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: ../database_gestion.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../database_gestion.php");
    exit();
}
?>