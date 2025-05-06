<?php
include_once 'header.php';
include_once '../database/database.php';

$get_activity_name = $pdo->prepare("SELECT name FROM TEAM_ACTIVITY WHERE id = :id");
$get_activity_name->bindParam(':id', $_GET['id']);
$get_activity_name->execute();
$activity_name = $get_activity_name->fetchColumn();

$activity_description = $pdo->prepare("SELECT description FROM TEAM_ACTIVITY WHERE id = :id");
$activity_description->bindParam(':id', $_GET['id']);
$activity_description->execute();
$team = $activity_description->fetchColumn();

$get_user_id_stmt = $pdo->prepare('SELECT id FROM USER WHERE email = :mail');
$get_user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$get_user_id_stmt->execute();
$get_user_id = $get_user_id_stmt->fetchColumn();

?>

<link rel="stylesheet" href="../css/view_petition.css">
<link rel="stylesheet" href="../css/benevoles_team.css">

<div class="team_header">
    <h1><?=$activity_name?></h1>
    <p><?=$team?></p>
    <hr class="line_header">
    <div class="btn_container">
    
    <div class="btn">
        <?php
        $get_team_of_activity = $pdo->prepare("SELECT id_team FROM TEAM_ACTIVITY WHERE id = :id");
        $get_team_of_activity->bindParam(':id', $_GET['id']);
        $get_team_of_activity->execute();
        $team_id = $get_team_of_activity->fetchColumn();
        ?>
        <a href="team.php?id=<?php echo $team_id ?>" class="quick"><img src="/Resources/img/ui_icons/back.png" alt="leader" class="btn_img">&nbsp;&nbsp;Retour</a>
        <?php
        $get_activity_max = $pdo->prepare("SELECT max_participants FROM TEAM_ACTIVITY WHERE id = :id");
        $get_activity_max->bindParam(':id', $_GET['id']);
        $get_activity_max->execute();
        $activity_max = $get_activity_max->fetchColumn();

        $current_participants = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id");
        $current_participants->bindParam(':id', $_GET['id']);
        $current_participants->execute();
        $current_participants_count = $current_participants->fetchColumn();

        if($activity_max != null && $current_participants_count < $activity_max){
            $check_if_already_subscribed = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id AND id_user = :user_id");
            $check_if_already_subscribed->bindParam(':id', $_GET['id']);
            $check_if_already_subscribed->bindParam(':user_id', $get_user_id);
            $check_if_already_subscribed->execute();
            $already_subscribed = $check_if_already_subscribed->fetchColumn();

            echo '<div class="void">&nbsp;</div>';

            if($already_subscribed == 0){
                echo '<a href="Processus/inscription_activity.php?id='.$_GET['id'].'" class="quick"><img src="/Resources/img/ui_icons/plus.png" alt="leader" class="btn_img">&nbsp;&nbsp;S\'inscrire</a>';
            } else {
                echo '<a href="Processus/unsubscribe_activity.php?id='.$_GET['id'].'" class="quick"><img src="/Resources/img/ui_icons/ban.png" alt="leader" class="btn_img">&nbsp;&nbsp;Se désinscrire</a>';
            }

        }

        $utilisateur_inscrit = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id AND id_user = :user_id");
        $utilisateur_inscrit->bindParam(':id', $_GET['id']);
        $utilisateur_inscrit->bindParam(':user_id', $get_user_id);
        $utilisateur_inscrit->execute();
        $inscrit = $utilisateur_inscrit->fetchColumn();

        ?>
    </div>
    </div>
</div>

<div class="container smaller_container">
    <div class="hierarchy smaller">
        <h2>Informations sur l'activité</h2>

        <?php

        $get_activity_location = $pdo->prepare("SELECT city, postal_code, rue, num FROM TEAM_ACTIVITY WHERE id = :id");
        $get_activity_location->bindParam(':id', $_GET['id']);
        $get_activity_location->execute();
        $activity_location = $get_activity_location->fetch(PDO::FETCH_ASSOC);

        if ($activity_location['city'] != null && $activity_location['postal_code'] != null && $activity_location['rue'] != null && $activity_location['num'] != null) {
            $city = $activity_location['city'];
            $postal_code = $activity_location['postal_code'];
            $rue = $activity_location['rue'];
            $num = $activity_location['num'];

            echo "<address>$num $rue, <br>$postal_code, <br>$city (France)</address>";

            $address = urlencode("$num $rue, $postal_code $city France");
            $maps_url = "https://www.google.com/maps?q=$address";

            echo "<a href='$maps_url' target='_blank' class='maps-link'>
            <img src='/Resources/img/ui_icons/loupe.png' alt='map'>
            Voir sur Google Maps
          </a>";

        } else {
            echo '<div class="msg">
                    <img src="/Resources/img/ui_icons/empty.png"  alt="empty">
                    <p class="txt">Aucune information sur le lieu</p>
                </div>';
        }

        ?>
        <hr class="line left_line">

        <?php

        $get_activity_date = $pdo->prepare("SELECT event_date FROM TEAM_ACTIVITY WHERE id = :id");
        $get_activity_date->bindParam(':id', $_GET['id']);
        $get_activity_date->execute();
        $activity_date = $get_activity_date->fetchColumn();

        $formatted_date = date('d/m/Y', strtotime($activity_date));

        if ($activity_date != null) {
            echo "<p class='date'>Date de l'activité : $formatted_date</p>";
        } else {
            echo '<div class="msg">
                    <img src="/Resources/img/ui_icons/empty.png"  alt="empty">
                    <p class="txt">Aucune information sur la date</p>
                </div>';
        }

        $max_participants = $pdo->prepare("SELECT max_participants FROM TEAM_ACTIVITY WHERE id = :id");
        $max_participants->bindParam(':id', $_GET['id']);
        $max_participants->execute();
        $max_participants = $max_participants->fetchColumn();

        $current_participants = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id"); 
        $current_participants->bindParam(':id', $_GET['id']);
        $current_participants->execute();
        $current_participants_count = $current_participants->fetchColumn();

        if ($max_participants != null) {
            
            echo "<hr class='line left_line'>";
            echo "<p class='max_participants'>$current_participants_count / $max_participants participants autorisés</p>";
            if($current_participants_count > 0){
                echo "<a href='Processus/download_list.php' target='_blank' class='maps-link'>
            <img src='/Resources/img/ui_icons/download.png' alt='map'>
            Télécharger la liste
          </a>";
          echo "<div class='void'>&nbsp;</div>";
            }
        }

        ?>

    </div>
    <div class="next_activities complete">
        <h3>Commentaires</h3>

        <div class="comment_section">

    <?php

        echo '
        <div class="new secondary">
            <form method="POST" action="Processus/add_comment.php">
                <input type="hidden" name="activity_id" value="
        ';
        echo $_GET['id'];
        echo '">
                <input type="hidden" name="user_id" value="';
        echo $get_user_id;
        echo '">
                <textarea name="comment" id="comment" maxlength=200  onkeyup="count(\'desc_counter\',this,200)" required></textarea>
                <div class="limit positioned" id="desc_counter">
                        <p>Limite de caractères : 0 / 200</p>
                </div>

                <button type="submit" class="comment_btn custom-button"><img src="/Resources/img/ui_icons/send.png" alt="Envoyer"><p>&nbsp; Publier</p></button>
            </form>
        </div>';

    ?>

    <?php

    $comments_stmt = $pdo->prepare('SELECT * FROM ACTIVITY_COMMENT WHERE id_activity = :id ORDER BY date DESC');
    $comments_stmt->bindParam(':id', $_GET['id']);
    $comments_stmt->execute();
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach($comments as $comment){
        $comment_user_stmt = $pdo->prepare('SELECT username FROM USER WHERE id = :id');
        $comment_user_stmt->bindParam(':id', $comment['id_user']);
        $comment_user_stmt->execute();
        $comment_user = $comment_user_stmt->fetchColumn();

        $get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE id = :id');
        $get_avatar_hat->bindParam(':id', $comment['id_user']);
        $get_avatar_hat->execute();
        $avatar_hat = $get_avatar_hat->fetchColumn();

        $get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE id = :id');
        $get_avatar_eyes->bindParam(':id', $comment['id_user']);
        $get_avatar_eyes->execute();
        $avatar_eyes = $get_avatar_eyes->fetchColumn();

        $get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE id = :id');
        $get_avatar_mouth->bindParam(':id', $comment['id_user']);
        $get_avatar_mouth->execute();
        $avatar_mouth = $get_avatar_mouth->fetchColumn();

        $get_avatar_skin = $pdo->prepare('SELECT avatar_skin FROM USER WHERE id = :id');
        $get_avatar_skin->bindParam(':id', $comment['id_user']);
        $get_avatar_skin->execute();
        $avatar_skin = $get_avatar_skin->fetchColumn();

        $get_avatar_hat_color = $pdo->prepare('SELECT avatar_hat_color FROM USER WHERE id = :id');
        $get_avatar_hat_color->bindParam(':id', $comment['id_user']);
        $get_avatar_hat_color->execute();
        $avatar_hat_color = $get_avatar_hat_color->fetchColumn();

        $get_avatar_eyes_color = $pdo->prepare('SELECT avatar_eyes_color FROM USER WHERE id = :id');
        $get_avatar_eyes_color->bindParam(':id', $comment['id_user']);
        $get_avatar_eyes_color->execute();
        $avatar_eyes_color = $get_avatar_eyes_color->fetchColumn();

        $get_avatar_mouth_color = $pdo->prepare('SELECT avatar_mouth_color FROM USER WHERE id = :id');
        $get_avatar_mouth_color->bindParam(':id', $comment['id_user']);
        $get_avatar_mouth_color->execute();
        $avatar_mouth_color = $get_avatar_mouth_color->fetchColumn();

        $get_avatar_skin_color = $pdo->prepare('SELECT avatar_skin_color FROM USER WHERE id = :id');
        $get_avatar_skin_color->bindParam(':id', $comment['id_user']);
        $get_avatar_skin_color->execute();
        $avatar_skin_color = $get_avatar_skin_color->fetchColumn();

        $formatted_date = date('d/m/Y', strtotime($comment['date']));
        $formated_time = date('H:i', strtotime($comment['date']));

        echo '<div class="comment">
                <div class="user_info">
                    <div class="avatar">
                        <img class="skin" src="/Resources/avatar/skin/skin'.$avatar_skin.'c'.$avatar_skin_color.'.png" alt="">
                        <img src="/Resources/avatar/hat/hat'.$avatar_hat.'c'.$avatar_hat_color.'.png" class="hat" alt="Hat" id="hat">
                        <img src="/Resources/avatar/eyes/eye'.$avatar_eyes.'c'.$avatar_eyes_color.'.png" class="eyes" alt="Eyes" id="eyes">
                        <img src="/Resources/avatar/mouth/smile'.$avatar_mouth.'c'.$avatar_mouth_color.'.png" class="mouth" alt="Mouth" id="mouth">
                    </div>
                    <a href="/Sources/view_profile.php?id='. $comment['id_user'] .'" class="profile_link_comment">'.$comment_user.'</a>
                </div>
                <div class="comment_content">
                    <p class="comment_date">'.$formatted_date.' &#x25CF; '.$formated_time.' &#x25CF ';

                    if($is_admin == 1){
                        echo '<a href="Processus/admin_delete_com.php?id='.$comment['id'].'" class="quick2">
                        <img src="/Resources/img/ui_icons/trash.png" alt=""></a>';
                    }

                    echo '
                    <a href="Processus/report.php?id='.$comment['id'].'&type=3" class="quick2">
                    <img src="/Resources/img/ui_icons/red-flag.png" alt=""></a>';

                    echo '</p>
                    <div class="comment_text">'.$comment['content'].'</div>
                </div>
            </div>';
    }

    ?>

    </div>
</div>

<script src="../js/count_characters.js"></script>