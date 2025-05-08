<?php
include_once 'header.php';
include_once '../database/database.php';

try{
    $get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $get_user_id->bindParam(':mail', $_SESSION['mail']);
    $get_user_id->execute();
    $get_user_id = $get_user_id->fetchColumn();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>

<link rel="stylesheet" href="../css/benevoles_index.css">
<link rel="stylesheet" href="../css/benevoles_team.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Mes équipes</h1>
            <hr class="main_hr">

            <div class="btn" id="create_team">
                <a href="" class="quick"><img src="/Resources/img/ui_icons/plus.png" alt="leader" class="btn_img">&nbsp;&nbsp;Créer mon équipe</a>
            </div>
            
            <?php

            $count_teams_of_user = $pdo->prepare("SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_user = :id_user");
            $count_teams_of_user->bindParam(':id_user', $get_user_id);
            $count_teams_of_user->execute();
            $count_teams_of_user = $count_teams_of_user->fetchColumn();

            if($count_teams_of_user != 0){
                $get_all_teams_of_user = $pdo->prepare("SELECT * FROM TEAM_MEMBER WHERE id_user = :id_user");
                $get_all_teams_of_user->bindParam(':id_user', $get_user_id);
                $get_all_teams_of_user->execute();
                $all_teams_ids = $get_all_teams_of_user->fetchAll(PDO::FETCH_ASSOC);

                $all_teams_of_user = [];

                foreach($all_teams_ids as $team_id){
                    $get_team = $pdo->prepare("SELECT * FROM TEAM WHERE id = :id");
                    $get_team->bindParam(':id', $team_id['id_team']);
                    $get_team->execute();
                    $all_teams_of_user[] = $get_team->fetch(PDO::FETCH_ASSOC);
                }

                foreach($all_teams_of_user as $team){

                    $get_leader = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                    $get_leader->bindParam(':id', $team['leader']);
                    $get_leader->execute();
                    $team['leader_username'] = $get_leader->fetchColumn();

                    echo '
                    <div class="test">
                        <h2>'. $team['name'] .'</h2>
                        <p class="name">Gérée par <a target="_blank" href="/Sources/view_profile.php?id='. $team['leader'] .'" class="link_to_profile">'. $team['leader_username'] .'</a></p>
                        <hr>
                        <p>'. $team['description'] .'</p>
                        <hr>
                        <button type="button" onclick="window.location.href=\'team.php?id='. $team['id'] .'\';" class="custom-button select_team"><img src="/Resources/img/ui_icons/greater.png" alt="">&nbsp;Go !</button>
                    </div>
                    ';
                }
            }else{
                echo '
                to do error
                ';
            }

            ?>
        </div>
    </div>

    

<?php
include_once 'footer.php';
?>
