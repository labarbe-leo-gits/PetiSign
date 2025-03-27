<?php

include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));

        if (!empty($name)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO CATEGORY (name) VALUES (:name)");
                $stmt->bindParam(':name', $name);

                $stmt->execute();

                header("Location: ../database_gestion.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            header("Location: ../add_category.php");
            exit();
        }
        }
    else {
        header("Location: ../add_category.php");
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}

?>