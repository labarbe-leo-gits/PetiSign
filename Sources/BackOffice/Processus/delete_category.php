<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        try {

            $count_number_of_categories = $pdo->prepare("SELECT COUNT(id) FROM CATEGORY");
            $count_number_of_categories->execute();
            $count = $count_number_of_categories->fetchColumn();

            if($count <= 1){
                //header("Location: ../database_gestion.php?error=last_category");
                echo "<script>window.location.href = '../database_gestion.php?error=last_category';</script>";
                exit();
            }

            $stmt = $pdo->prepare("DELETE FROM CATEGORY WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $delete_all_petitions_inside_this_category = $pdo->prepare("DELETE FROM PETITION WHERE category = :id");
            $delete_all_petitions_inside_this_category->bindParam(':id', $id, PDO::PARAM_INT);
            $delete_all_petitions_inside_this_category->execute();

            header("Location: ../database_gestion.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        header("Location: ../database_gestion.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>