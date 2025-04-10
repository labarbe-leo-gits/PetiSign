<?php
include_once 'security.php';
include_once '../database/database.php';

if($is_benevole !=0){

    echo $_POST['date'];

    try{

        if(isset($_POST['title']) && isset($_POST['date']) && isset($_POST['description'])){

            $team_id = $_POST['id'];

            if($team_id == null || $team_id <= 0 || empty($team_id) || !is_numeric($team_id) || !$team_id){
                echo "Invalid team ID";
                exit();
            }

            $event_name = htmlspecialchars($_POST['title']);
            $event_date = $_POST['date'];
            $event_description = htmlspecialchars($_POST['description']);

            $user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
            $user_id->bindParam(':mail', $_SESSION['mail']);
            $user_id->execute();
            $user_id = $user_id->fetchColumn();

            // Insert the new event into the database
            $stmt = $pdo->prepare("INSERT INTO TEAM_ACTIVITY (name, event_date, description, id_user, id_team) VALUES (:name, :date, :description, :id_user, :id_team)");
            $stmt->bindParam(':name', $event_name);
            $stmt->bindParam(':date', $event_date);
            $stmt->bindParam(':description', $event_description);
            $stmt->bindParam(':id_user', $user_id);
            $stmt->bindParam(':id_team', $team_id);
            $stmt->execute();

            echo "Event created successfully";
            exit();

            
        }else{
            header('Location: ./Sources/error.php?error=All fields are required');
            exit();
        }
    }catch(PDOException $e){
        echo "Error: " . $e->getMessage();
        exit();
    }
}else{
    header('Location: ./Sources/error.php?error=403');
    exit();
}

?>