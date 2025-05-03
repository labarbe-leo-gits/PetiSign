<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {
            $stmt = $pdo->prepare("DELETE FROM CAPTCHA WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            header("Location: ../captcha.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../captcha.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>