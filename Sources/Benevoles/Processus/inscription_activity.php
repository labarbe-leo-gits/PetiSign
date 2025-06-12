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

    $check_if_max_participants = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id_activity");
    $check_if_max_participants->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
    $check_if_max_participants->execute();
    $max_participants = $check_if_max_participants->fetchColumn();

    $check_if_activity_is_full = $pdo->prepare("SELECT max_participants FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $check_if_activity_is_full->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
    $check_if_activity_is_full->execute();
    $max_participants_activity = $check_if_activity_is_full->fetchColumn();

    $target_activity_event_date = $pdo->prepare("SELECT event_date FROM TEAM_ACTIVITY WHERE id = :id_activity");
    $target_activity_event_date->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
    $target_activity_event_date->execute();
    $activity_event_date = $target_activity_event_date->fetchColumn();

    $today = date('Y-m-d');

    if($activity_event_date <= $today){
        header("Location: ../view_activity.php?id=$filtered_activity_id");
        exit();
    }

    if($max_participants >= $max_participants_activity){
        echo "<script>alert('Inscription échouée : le nombre maximum de participants a été atteint !');</script>";
        header("Location: ../view_activity.php?id=$filtered_activity_id");
        exit();
    }

    $check_if_user_is_inside_team = $pdo->prepare("SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_team = :id_team AND id_user = :id_user");
    $check_if_user_is_inside_team->bindParam(':id_team', $result, PDO::PARAM_INT);
    $check_if_user_is_inside_team->bindParam(':id_user', $user_id, PDO::PARAM_INT);
    $check_if_user_is_inside_team->execute();
    $is_user_inside_team = $check_if_user_is_inside_team->fetchColumn();

    if($is_user_inside_team != 0){
        $insert_inscription = $pdo->prepare("INSERT INTO ACTIVITY_INSCRIPTION (id_user, id_activity) VALUES (:id_user, :id_activity)");
        $insert_inscription->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $insert_inscription->bindParam(':id_activity', $filtered_activity_id, PDO::PARAM_INT);
        $insert_inscription->execute();

        echo "<script>alert('Inscription réussie !');</script>";

        header("Location: ../view_activity.php?id=$filtered_activity_id");
        exit();

    }

}

?>