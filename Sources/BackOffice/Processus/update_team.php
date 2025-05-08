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
            $team_id = filter_input(INPUT_POST, 'team_id', FILTER_SANITIZE_NUMBER_INT);

            $all_bases_benevoles = $pdo->prepare("SELECT id_user FROM TEAM_MEMBER WHERE id_team = :id_team");
            $all_bases_benevoles->bindParam(':id_team', $team_id);
            $all_bases_benevoles->execute();
            $all_bases_benevoles = $all_bases_benevoles->fetchAll(PDO::FETCH_COLUMN);

            $base_leader = $pdo->prepare("SELECT leader FROM TEAM WHERE id = :id_team");
            $base_leader->bindParam(':id_team', $team_id);
            $base_leader->execute();
            $base_leader = $base_leader->fetchColumn();

            $base_infos = $pdo->prepare("SELECT name, sector, description FROM TEAM WHERE id = :id_team");
            $base_infos->bindParam(':id_team', $team_id);
            $base_infos->execute();
            $base_infos = $base_infos->fetch(PDO::FETCH_ASSOC);

            if($team_name != $base_infos['name']) {
                $update_team = $pdo->prepare("UPDATE TEAM SET name = :name WHERE id = :id_team");
                $update_team->bindParam(':name', $team_name);
                $update_team->bindParam(':id_team', $team_id);
                $update_team->execute();
            }
            
            if($sector != $base_infos['sector']) {
                $update_team = $pdo->prepare("UPDATE TEAM SET sector = :sector WHERE id = :id_team");
                $update_team->bindParam(':sector', $sector);
                $update_team->bindParam(':id_team', $team_id);
                $update_team->execute();
            }

            if($team_leader != $base_leader) {
                $update_team = $pdo->prepare("UPDATE TEAM SET leader = :leader WHERE id = :id_team");
                $update_team->bindParam(':leader', $team_leader);
                $update_team->bindParam(':id_team', $team_id);
                $update_team->execute();

                $count = $pdo->prepare("SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user");
                $count->bindParam(':id_team', $team_id);
                $count->bindParam(':id_user', $team_leader);
                $count->execute();
                $count = $count->fetchColumn();

                if ($count == 0) {
                    $assign_to_team = $pdo->prepare("INSERT INTO TEAM_MEMBER (id_team, id_user) VALUES (:id_team, :id_user)");
                    $assign_to_team->bindParam(':id_team', $team_id, PDO::PARAM_INT);
                    $assign_to_team->bindParam(':id_user', $team_leader, PDO::PARAM_INT);
                    $assign_to_team->execute();
                }

            }

            $benevoleIds = explode(',', $selectedBenevoles);

            foreach ($all_bases_benevoles as $existing_id) {
                if (!in_array($existing_id, $benevoleIds)) {
                    $delete_benevole = $pdo->prepare("DELETE FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user");
                    $delete_benevole->bindParam(':id_team', $team_id);
                    $delete_benevole->bindParam(':id_user', $existing_id);
                    $delete_benevole->execute();
                }
            }

            foreach ($benevoleIds as $id) {
                $id = intval($id);
                if ($id > 0 && !in_array($id, $all_bases_benevoles)) {
                    $assign_to_team = $pdo->prepare("INSERT INTO TEAM_MEMBER (id_team, id_user) VALUES (:id_team, :id_user)");
                    $assign_to_team->bindParam(':id_team', $team_id, PDO::PARAM_INT);
                    $assign_to_team->bindParam(':id_user', $id, PDO::PARAM_INT);
                    $assign_to_team->execute();
                }
            }

            header('Location: ../teams.php');
            exit();
            
        } catch (Exception $e) {
            error_log("Error updating team: " . $e->getMessage());
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>