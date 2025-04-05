<?php
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {

            $abonnement_stmt = $pdo->prepare("DELETE FROM ABONNEMENT WHERE id_newsletter = :id");
            $abonnement_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $abonnement_stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM NEWSLETTER WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../newsletter.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../newsletter.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>