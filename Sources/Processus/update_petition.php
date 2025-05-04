<?php
include_once '../loading.php';
include_once '../database/database.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../my_petitions.php');
    exit();
}

session_start();
if(!isset($_SESSION['mail'])){
    header('Location: ../login.php');
    exit();
}

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$user_id = $get_user_id_stmt->fetchColumn();

$get_user_petitions_id = $pdo->prepare("SELECT id FROM PETITION WHERE user = :user_id");
$get_user_petitions_id->bindParam(':user_id', $user_id);
$get_user_petitions_id->execute();
$petitions_id = $get_user_petitions_id->fetchAll();

$pet_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

$get_petition_goal_before_update = $pdo->prepare("SELECT signature_goal FROM PETITION WHERE id = :id");
$get_petition_goal_before_update->bindParam(':id', $pet_id);
$get_petition_goal_before_update->execute();
$petition_goal_before_update = $get_petition_goal_before_update->fetchColumn();

$clean_array = array();

foreach ($petitions_id as $key => $value) {
    $clean_array[] = $value['id'];
}

if(!in_array($pet_id, $clean_array)){
    header('Location: ../my_petitions.php');
    exit();
}

$pet_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$pet_desc = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$pet_goal = filter_input(INPUT_POST, 'objectif', FILTER_SANITIZE_NUMBER_INT);
$pet_goal = filter_var($pet_goal, FILTER_VALIDATE_INT);

if(empty($pet_name) || empty($pet_desc) || empty($pet_goal) || !$pet_name || !$pet_desc || !$pet_goal){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();
}

$pet_name_length = strlen($pet_name);
$pet_desc_length = strlen($pet_desc);

if($pet_name_length > 60 || $pet_desc_length > 800 || $pet_goal < 10){
    header('Location: '.$_SERVER['HTTP_REFERER']);
    exit();
}

$signature_count_stmt = $pdo->prepare("SELECT signature_count FROM PETITION WHERE id = :id");
$signature_count_stmt->bindParam(':id', $pet_id);
$signature_count_stmt->execute();
$signature_count = $signature_count_stmt->fetchColumn();

try {

    if($pet_goal < $signature_count){
        header('Location: '.$_SERVER['HTTP_REFERER']);
        exit();
    }

    $update_petition_stmt = $pdo->prepare("UPDATE PETITION SET title = :title, description = :description, signature_goal = :sign_goal WHERE id = :id");
    $update_petition_stmt->bindParam(':title', $pet_name);
    $update_petition_stmt->bindParam(':description', $pet_desc);
    $update_petition_stmt->bindParam(':sign_goal', $pet_goal);
    $update_petition_stmt->bindParam(':id', $pet_id);
    
    if($update_petition_stmt->execute()){

        $get_petition_goal_after_update = $pdo->prepare("SELECT signature_goal FROM PETITION WHERE id = :id");
        $get_petition_goal_after_update->bindParam(':id', $pet_id);
        $get_petition_goal_after_update->execute();
        $petition_goal_after_update = $get_petition_goal_after_update->fetchColumn();

        if($petition_goal_after_update < $petition_goal_before_update && $signature_count < $petition_goal_after_update){
            $petition_stage_one = ($petition_goal_after_update / 4) * 1;
            $petition_stage_two = ($petition_goal_after_update / 4) * 2;
            $petition_stage_three = ($petition_goal_after_update / 4) * 3;
            $petition_stage_four = $petition_goal_after_update;
            
            $update_stages_stmt = $pdo->prepare("UPDATE PETITION SET signature_stage_one = :stage_one, signature_stage_two = :stage_two, signature_stage_three = :stage_three, signature_stage_four = :stage_four WHERE id = :id");
            $update_stages_stmt->bindParam(':stage_one', $petition_stage_one);
            $update_stages_stmt->bindParam(':stage_two', $petition_stage_two);
            $update_stages_stmt->bindParam(':stage_three', $petition_stage_three);
            $update_stages_stmt->bindParam(':stage_four', $petition_stage_four);
            $update_stages_stmt->bindParam(':id', $pet_id);
            $update_stages_stmt->execute();
        }

        if($petition_goal_after_update > $petition_goal_before_update){

            $petition_stage_one = ($petition_goal_after_update / 4) * 1;
            $petition_stage_two = ($petition_goal_after_update / 4) * 2;
            $petition_stage_three = ($petition_goal_after_update / 4) * 3;
            $petition_stage_four = $petition_goal_after_update;
            
            $update_stages_stmt = $pdo->prepare("UPDATE PETITION SET signature_stage_one = :stage_one, signature_stage_two = :stage_two, signature_stage_three = :stage_three, signature_stage_four = :stage_four WHERE id = :id");
            $update_stages_stmt->bindParam(':stage_one', $petition_stage_one);
            $update_stages_stmt->bindParam(':stage_two', $petition_stage_two);
            $update_stages_stmt->bindParam(':stage_three', $petition_stage_three);
            $update_stages_stmt->bindParam(':stage_four', $petition_stage_four);
            $update_stages_stmt->bindParam(':id', $pet_id);
            $update_stages_stmt->execute();
            
            if($petition_goal_before_update == $signature_count){
                $update_status_stmt = $pdo->prepare("UPDATE PETITION SET statut = 'OPEN' WHERE id = :id");
                $update_status_stmt->bindParam(':id', $pet_id);
                $update_status_stmt->execute();
            }
        }

        if($petition_goal_after_update < $petition_goal_before_update && $signature_count == $petition_goal_after_update){
            $update_status_stmt = $pdo->prepare("UPDATE PETITION SET statut = 'CLOSED', signature_count = :limit_count WHERE id = :id");
            $update_status_stmt->bindParam(':limit_count', $petition_goal_after_update);
            $update_status_stmt->bindParam(':id', $pet_id);
            $update_status_stmt->execute();

        }


        echo "Petition updated successfully.";
        exit();
    } else {
        echo "Error updating petition.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>