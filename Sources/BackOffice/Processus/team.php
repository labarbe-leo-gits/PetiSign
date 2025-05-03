<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if ($is_admin != 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {

            $team_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $team_leader = filter_input(INPUT_POST, 'selected_leader', FILTER_SANITIZE_NUMBER_INT);
            $sector = filter_input(INPUT_POST, 'sector', FILTER_SANITIZE_STRING);
            $selectedBenevoles = filter_input(INPUT_POST, 'selected_benevoles', FILTER_SANITIZE_STRING);

            if (empty($team_name)) {
                header('Location: ../add_team.php?error=team_name');
                exit();
            }

            if (empty($team_leader) || !is_numeric($team_leader)) {
                header('Location: ../add_team.php?error=team_leader');
                exit();
            }

            $create_team_stmt = $pdo->prepare("INSERT INTO TEAM (name, leader) VALUES (:name, :leader)");
            $create_team_stmt->bindParam(':name', $team_name);
            $create_team_stmt->bindParam(':leader', $team_leader);
            $create_team_stmt->execute();

            $team_id = $pdo->lastInsertId();
            if (!$team_id) {
                throw new Exception("Failed to create team.");
            }

            if (!empty($sector)) {
                $update_team_stmt = $pdo->prepare("UPDATE TEAM SET sector = :sector WHERE id = :id");
                $update_team_stmt->bindParam(':sector', $sector);
                $update_team_stmt->bindParam(':id', $team_id, PDO::PARAM_INT);
                $update_team_stmt->execute();
            }

            if (!empty($selectedBenevoles) && $selectedBenevoles !== '0') {
                $benevoleIds = explode(',', $selectedBenevoles);
                foreach ($benevoleIds as $id) {
                    $id = intval($id);
                    if ($id > 0) {
                        $assign_to_team = $pdo->prepare("INSERT INTO TEAM_MEMBER (id_team, id_user) VALUES (:id_team, :id_user)");
                        $assign_to_team->bindParam(':id_team', $team_id, PDO::PARAM_INT);
                        $assign_to_team->bindParam(':id_user', $id, PDO::PARAM_INT);
                        $assign_to_team->execute();
                    }
                }
            }

            $assign_leader = $pdo->prepare("INSERT INTO TEAM_MEMBER (id_team, id_user) VALUES (:id_team, :id_user)");
            $assign_leader->bindParam(':id_team', $team_id, PDO::PARAM_INT);
            $assign_leader->bindParam(':id_user', $team_leader, PDO::PARAM_INT);
            $assign_leader->execute();

            header('Location: ../teams.php?level=success');
            exit();
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            header('Location: /Sources/error.php?code=500');
            exit();
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            header('Location: /Sources/error.php?code=500');
            exit();
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}

?>