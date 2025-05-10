<?php

include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

$team_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$team_sector = filter_input(INPUT_POST, 'sector', FILTER_SANITIZE_STRING);
$selectedBenevoles = filter_input(INPUT_POST, 'selected_benevoles', FILTER_SANITIZE_STRING);

$team_leader_id = $user_id;

if(empty($team_name)){
    echo "Le nom de l'équipe est vide.";
    exit();
}

$create_team_stmt = $pdo->prepare("INSERT INTO TEAM (name, leader) VALUES (:name, :leader)");
$create_team_stmt->bindParam(':name', $team_name);
$create_team_stmt->bindParam(':leader', $team_leader_id);
$create_team_stmt->execute();
$team_id = $pdo->lastInsertId();

echo "insertion ok";

if(!$team_id){
    echo "Erreur lors de la création de l'équipe.";
    exit();
}

if(!empty($team_sector)){
    $update_team_stmt = $pdo->prepare("UPDATE TEAM SET sector = :sector WHERE id = :id");
    $update_team_stmt->bindParam(':sector', $team_sector);
    $update_team_stmt->bindParam(':id', $team_id);
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
$assign_leader->bindParam(':id_user', $team_leader_id, PDO::PARAM_INT);
$assign_leader->execute();

header('Location: ../team.php?id=' . $team_id);
exit();

?>