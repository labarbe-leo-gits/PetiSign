<?php

include_once '../../loading.php';
include_once 'security.php';
include_once '../database/database.php';

$team_id = $_GET['id'] ?? null;
$filtered_team_id = filter_var($team_id, FILTER_SANITIZE_NUMBER_INT);

if ($filtered_team_id === null || $filtered_team_id === false) {
    echo json_encode(['error' => 'Invalid team ID']);
    exit;
}

$check_if_user_in_team = $pdo->prepare('SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user');
$check_if_user_in_team->bindParam(':id_team', $filtered_team_id, PDO::PARAM_INT);
$check_if_user_in_team->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$check_if_user_in_team->execute();
$user_in_team_count = $check_if_user_in_team->fetchColumn();

if ($user_in_team_count == 0) {
    echo "<p class='error'>You are not a member of this team</p>";
    exit;
}

$leave_team = $pdo->prepare('DELETE FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user');
$leave_team->bindParam(':id_team', $filtered_team_id, PDO::PARAM_INT);
$leave_team->bindParam(':id_user', $user_id, PDO::PARAM_INT);
$leave_team->execute();

if ($leave_team->rowCount() > 0) {
    echo "<p class='success'>You have successfully left the team</p>";;
} else {
    echo "<p class='error'>Failed to leave the team</p>";
}

?>