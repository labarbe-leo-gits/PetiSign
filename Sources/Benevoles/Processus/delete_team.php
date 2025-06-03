<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_benevole != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {

            $stmt = $pdo->prepare("DELETE FROM COMMENT WHERE id_target IN (SELECT id FROM TEAM_ACTIVITY WHERE id_team = :id) AND target_type = 2");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_activity IN (SELECT id FROM TEAM_ACTIVITY WHERE id_team = :id)");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM TEAM_ACTIVITY WHERE id_team = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM TEAM_MEMBER WHERE id_team = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM TEAM WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../index.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../index.php?error=missing_id");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>