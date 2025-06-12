<?php

include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if ($id_benevole != 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {

            $team_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $sector = filter_input(INPUT_POST, 'sector', FILTER_SANITIZE_STRING);
            $selectedBenevoles = filter_input(INPUT_POST, 'selected_benevoles', FILTER_SANITIZE_STRING);
            $team_id = filter_input(INPUT_POST, 'team_id', FILTER_SANITIZE_NUMBER_INT);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

            $all_bases_benevoles = $pdo->prepare("SELECT id_user FROM TEAM_MEMBER WHERE id_team = :id_team");
            $all_bases_benevoles->bindParam(':id_team', $team_id);
            $all_bases_benevoles->execute();
            $all_bases_benevoles = $all_bases_benevoles->fetchAll(PDO::FETCH_COLUMN);

            $base_infos = $pdo->prepare("SELECT name, sector, description FROM TEAM WHERE id = :id_team");
            $base_infos->bindParam(':id_team', $team_id);
            $base_infos->execute();
            $base_infos = $base_infos->fetch(PDO::FETCH_ASSOC);

            $get_team_leader_id = $pdo->prepare("SELECT leader FROM TEAM WHERE id = :team_id");
            $get_team_leader_id->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $get_team_leader_id->execute();
            $team_leader_id = $get_team_leader_id->fetchColumn();

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

            if($description != $base_infos['description']) {

                if(empty($description)) {
                    $description = "Aucune description disponible";
                }

                $update_team = $pdo->prepare("UPDATE TEAM SET description = :description WHERE id = :id_team");
                $update_team->bindParam(':description', $description);
                $update_team->bindParam(':id_team', $team_id);
                $update_team->execute();
            }

            $benevoleIds = explode(',', $selectedBenevoles);

            foreach ($all_bases_benevoles as $existing_id) {
                if (!in_array($existing_id, $benevoleIds) && $existing_id != $team_leader_id) {
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

            header('Location: ../team.php?id=' . $team_id . '&success_code=UpdSuccess');

        } catch (Exception $e) {
            header('Location: /Sources/error.php?code=500');
            exit();
        }
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>