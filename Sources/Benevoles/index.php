<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


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

$count_how_many_submissions = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE id_user = :id_user AND current_status = 'En Attente'");
$count_how_many_submissions->bindParam(':id_user', $get_user_id);
$count_how_many_submissions->execute();
$count_how_many_submissions = $count_how_many_submissions->fetchColumn();

if(isset($_POST['form_submit'])){

    $user_motivation = htmlspecialchars($_POST['description']);
    $filtered_motivation = htmlspecialchars($user_motivation, ENT_QUOTES, 'UTF-8');

    if(strlen($filtered_motivation) < 10 || strlen($filtered_motivation) > 120){
        header('Location: index.php?code=ben_form_error');
        exit();
    }

    $get_captcha_answer_from_id = $pdo->prepare("SELECT answer FROM CAPTCHA WHERE id = :id");
    $get_captcha_answer_from_id->bindParam(':id', $_SESSION['captcha']);
    $get_captcha_answer_from_id->execute();
    $captcha_answer = $get_captcha_answer_from_id->fetchColumn();

    $lowercase_captcha_answer = strtolower($captcha_answer);
    $lowercase_objectif = strtolower(htmlspecialchars($_POST['objectif']));

    if(/* $captcha_answer == htmlspecialchars($_POST['objectif']) */  $lowercase_captcha_answer == $lowercase_objectif){

        $candidate_insertion = $pdo->prepare("INSERT INTO USER_CANDIDATE (id_user, motivation, current_status, candidate_type) VALUES (:id_user, :motivation, 'En Attente', 1)");
        $candidate_insertion->bindParam(':id_user', $get_user_id);
        $candidate_insertion->bindParam(':motivation', $filtered_motivation);
        $candidate_insertion->execute();

        unset($_SESSION['captcha']);

        header('Location: index.php?code=ben_form_success');
    }
}

?>

<link rel="stylesheet" href="../css/create_petition.css">
<link rel="stylesheet" href="../css/benevoles_index.css">
<link rel="stylesheet" href="../css/benevoles_team.css">

<?php if($is_benevole != 0):?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Mes équipes</h1>
            <hr class="main_hr">

            <div class="btn" id="create_team">
                <a href="create_my_team.php" class="quick"><img src="/Resources/img/ui_icons/plus.png" alt="leader" class="btn_img">&nbsp;&nbsp;Créer mon équipe</a>
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
                <div class="test" id="error_msg">
                    <h2>Vous n\'avez pas encore d\'équipe !</h2>
                    <p class="name">Vous pouvez en créer une en cliquant sur le bouton ci-dessus, ou contactez les gérants des équipes existantes !</p>
                </div>
                ';

                $select_three_random_teams = $pdo->prepare("SELECT * FROM TEAM ORDER BY RAND() LIMIT 3");
                $select_three_random_teams->execute();
                $random_teams = $select_three_random_teams->fetchAll(PDO::FETCH_ASSOC);

                foreach($random_teams as $team){

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
                        <button type="button" onclick="window.location.href=\'/Sources/Processus/create_chat_feed.php?create_direct_feed=true&target_user_id='.$team['leader'].'\';" class="custom-button select_team"><img src="/Resources/img/ui_icons/greater.png" alt="">&nbsp;Contacter le gérant</button>
                    </div>
                    ';
                }

            }

            ?>
        </div>
    </div>
<?php endif?>
<?php
$get_benevole_status = $pdo->prepare("SELECT is_benevole FROM USER WHERE id = :id_user");
$get_benevole_status->bindParam(':id_user', $get_user_id);
$get_benevole_status->execute();
$is_benevole = $get_benevole_status->fetchColumn();
?>
<?php if($is_benevole == 0 && $count_how_many_submissions <=0):?>
    <?php
    
    $select_random_captcha = $pdo->prepare("SELECT * FROM CAPTCHA ORDER BY RAND() LIMIT 1");
    $select_random_captcha->execute();
    $captcha = $select_random_captcha->fetch(PDO::FETCH_ASSOC);

    $_SESSION['captcha'] = $captcha['id'];

    ?>
    <div class="container">

    <form method="post" action="index.php" id="become_benevole_form">
    <h1>Postulez pour être bénévole dès maintenant ! </h1>
    <hr class="main_hr">
        <div class="entries">
            <div class="entries">
                <div class="area">
                    <textarea required name="description" id="description" maxlength=120 onkeyup="count('desc_counter',this,800)"></textarea>
                    <label for="description" class="textarea_label">Motivation</label>
                </div>
                <div class="limit positioned" id="desc_counter">
                    <p>Limite de caractères : 0 / 120</p>
                </div>
            </div>
            <p class="question"><?=$captcha['question']?></p>
            <div class="entries">
                <input name="objectif" id="objectif" type="text" required placeholder=" ">
                <label for="objectif">Réponse</label>
            </div>
        </div>
        <hr class="form_hr">
        <input type="hidden" name="user_id" value="<?=$get_user_id?>">
        <button type="submit" name="form_submit" class="custom-button validate"><img src="/Resources/img/ui_icons/hour-glass.png" alt="">Postuler</button>
    </form>
                </div>
            </div>
        </div>
<?php endif?>

<?php
$get_submission_status = $pdo->prepare("SELECT current_status FROM USER_CANDIDATE WHERE id_user = :id_user");
$get_submission_status->bindParam(':id_user', $get_user_id);
$get_submission_status->execute();
$current_status = $get_submission_status->fetchColumn();
?>
    
<?php if($count_how_many_submissions > 0 && $current_status != "Accepté"):?>
    <div class="container">
        <div class="information">
            <img src="/Resources/img/ui_icons/validate.png" alt="">
            <p>Votre candidature a bien été transmise à nos équipes. Vous serez informé de son suivi via l'adresse mail liée à votre compte.</p>
        </div>
    </div>
    <button type="button" class="custom-button ben_btn" onclick="window.location.href='../'">Retourner à l'accueil</button>
<?php endif?>
<script src="../js/count_characters.js"></script>
<?php
include_once 'footer.php';
?>
