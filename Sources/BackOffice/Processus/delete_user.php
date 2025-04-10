<?php
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $get_current_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
        $get_current_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
        $get_current_user_id->execute();
        $current_user_id = $get_current_user_id->fetchColumn();

        if ($current_user_id == $id) {
            echo "cannot delete your own account";
            exit();
        }

        try {

            $stmt = $pdo->prepare("DELETE FROM PETITION WHERE user = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare("DELETE FROM USER WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: ../users.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../users.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>