<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {

            $count_how_many_captchas_are_in_database = $pdo->prepare("SELECT COUNT(*) FROM CAPTCHA WHERE state = 1");
            $count_how_many_captchas_are_in_database->execute();
            $count = $count_how_many_captchas_are_in_database->fetchColumn();

            if ($count <= 1) {
                echo "<script>window.location.href = '../captcha.php?error=TooFew';</script>";
                exit();
            }

            $stmt = $pdo->prepare("UPDATE CAPTCHA SET state = 0 WHERE id = :id");
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