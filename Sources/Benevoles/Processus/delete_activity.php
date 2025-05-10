<?php
include_once '../../loading.php';
include_once 'security.php';
include_once '../database/database.php';

if($is_benevole !=0){

    $activity_id = $_GET['id'];
    $filtered_id = filter_var($activity_id, FILTER_SANITIZE_NUMBER_INT);

    $get_activity_owner = $pdo->prepare("SELECT id_user FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $get_activity_owner->bindParam(':id_activity', $filtered_id, PDO::PARAM_INT);
    $get_activity_owner->execute();
    $activity_owner = $get_activity_owner->fetchColumn();

    $get_team_id = $pdo->prepare("SELECT id_team FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $get_team_id->bindParam(':id_activity', $filtered_id, PDO::PARAM_INT);
    $get_team_id->execute();
    $team_id = $get_team_id->fetchColumn();

    if($activity_owner == $user_id){

        $delete_all_inscriptions = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id_activity");
        $delete_all_inscriptions->bindParam(':id_activity', $filtered_id, PDO::PARAM_INT);
        $delete_all_inscriptions->execute();

        $delete_all_comments = $pdo->prepare("DELETE FROM COMMENT WHERE id_target = :id_activity AND target_type = 2");
        $delete_all_comments->bindParam(':id_activity', $filtered_id, PDO::PARAM_INT);
        $delete_all_comments->execute();

        $delete_activity = $pdo->prepare("DELETE FROM TEAM_ACTIVITY WHERE id = :id_activity");
        $delete_activity->bindParam(':id_activity', $filtered_id, PDO::PARAM_INT);
        $delete_activity->execute();

        header('Location: ../team.php?id='.$team_id.'&code=DelSuccess');
        exit();
    }


}else{
    header('Location: ./Sources/error.php?error=403');
    exit();
}

?>