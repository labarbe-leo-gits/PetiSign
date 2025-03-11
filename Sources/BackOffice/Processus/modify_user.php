<?php

include_once '../../database/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $admin = filter_input(INPUT_POST, 'administrator', FILTER_SANITIZE_NUMBER_INT);
    $benevole = filter_input(INPUT_POST, 'benevole', FILTER_SANITIZE_NUMBER_INT);

    if (isset($id, $username, $admin, $benevole)) {
        try {
            $stmt = $pdo->prepare("UPDATE USER SET is_admin = :is_admin, is_benevole = :is_benevole, username = :username WHERE id = :id");
            $stmt->bindParam(':is_admin', $admin);
            $stmt->bindParam(':is_benevole', $benevole);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            header("Location: ../users.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->POSTMessage();
        }
    } else {
        header("Location: ../modify_user_form.php?id=$id");
        exit();
    }
} else {
    header("Location: ../users.php");
    exit();
}

?>