<?php

include_once '../../loading.php';
include_once 'security.php';
include_once '../database/database.php';

if($is_benevole !=0){

    $activity_id = $_GET['id'];
    $filtered_activity_id = filter_var($activity_id, FILTER_SANITIZE_NUMBER_INT);

    $get_team_id = $pdo->prepare("SELECT id_team FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $get_team_id->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
    $get_team_id->execute();
    $result = $get_team_id->fetchColumn();

    $check_if_user_is_inside_team = $pdo->prepare("SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user");
    $check_if_user_is_inside_team->bindParam(':id_team', $result, PDO::PARAM_INT);
    $check_if_user_is_inside_team->bindParam(':id_user', $user_id, PDO::PARAM_INT);
    $check_if_user_is_inside_team->execute();
    $is_user_inside_team = $check_if_user_is_inside_team->fetchColumn();

    if($is_user_inside_team != 0){

        $check_if_user_is_already_inscribed = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_user = :id_user AND id_activity = :id_activity");
        $check_if_user_is_already_inscribed->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $check_if_user_is_already_inscribed->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
        $check_if_user_is_already_inscribed->execute();
        $is_user_already_inscribed = $check_if_user_is_already_inscribed->fetchColumn();

        if($is_user_already_inscribed != 0){

            $delete_inscription = $pdo->prepare("DELETE FROM ACTIVITY_INSCRIPTION WHERE id_user = :id_user AND id_activity = :id_activity");
            $delete_inscription->bindParam(':id_user', $user_id, PDO::PARAM_INT);
            $delete_inscription->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
            $delete_inscription->execute();

            //echo "<script>alert('Désinscription réussie !');</script>";
            header("Location: ../view_activity.php?id=$filtered_activity_id");
            exit();

        } else {
            //echo "<script>alert('Vous n\'êtes pas inscrit à cette activité !');</script>";
            header("Location: ../view_activity.php?id=$filtered_activity_id");
            exit();
        }

    }

}

?>