<?php

include_once '../../database/database.php';
include_once 'security.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $sector = htmlspecialchars(filter_input(INPUT_POST, 'sector', FILTER_SANITIZE_STRING));

    if (!empty($question) && !empty($answer)) {
        if (strlen($question) <= 255) {
            try {
                $stmt = $pdo->prepare("INSERT INTO TEAMS (name, sector) VALUES (:name, :sector)");
                $stmt->bindParam(':question', $name);
                $stmt->bindParam(':answer', $sector);

                $stmt->execute();

                header("Location: ../teams.php");
                exit();
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            header("Location: ../add_team.php");
            exit();
        }
    } else {
        header("Location: ../add_team.php");
        exit();
    }
} else {
    header("Location: ../add_team.php");
    exit();
}

?>