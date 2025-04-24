<?php

include_once 'header.php';
include_once '../database/database.php';

$team_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($team_id === null || $team_id === false || $team_id <= 0 || !is_numeric($team_id) || $team_id == '') {
    echo "Invalid team ID.";
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM TEAM WHERE id = :id");
$stmt->bindParam(':id', $team_id, PDO::PARAM_INT);
$stmt->execute();
$count = $stmt->fetchColumn();

if ($count != 1) {
    echo "Team ID does not exist.";
    exit;
}

$get_user_id = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$get_user_id->bindParam(':mail', $_SESSION['mail'], PDO::PARAM_STR);
$get_user_id->execute();
$user_id = $get_user_id->fetchColumn();

$check_if_user_in_team = $pdo->prepare("SELECT COUNT(*) FROM TEAM_MEMBER WHERE id_user = :user_id AND id_team = :team_id");
$check_if_user_in_team->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$check_if_user_in_team->bindParam(':team_id', $team_id, PDO::PARAM_INT);
$check_if_user_in_team->execute();
$check_if_user_in_team = $check_if_user_in_team->fetchColumn();

if ($check_if_user_in_team != 1) {
    echo "You are not a member of this team.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM TEAM WHERE id = :id");
$stmt->bindParam(':id', $team_id, PDO::PARAM_INT);
$stmt->execute();
$team = $stmt->fetch(PDO::FETCH_ASSOC);

$leader_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$leader_username->bindParam(':id', $team['leader'], PDO::PARAM_INT);
$leader_username->execute();
$leader_username = $leader_username->fetchColumn();

if ($team['leader'] == $user_id) {
    $is_leader = 1;
} else {
    $is_leader = 0;
}

?>

<link rel="stylesheet" href="../css/benevoles_team.css">

<div class="team_header">
    <h1><?=$team['name']?></h1>
    <p><?=$team['description']?></p>
    <hr class="line_header">
    <div class="btn_container">
    <?php

    if ($is_leader == 1) {
        echo '
        <div class="btn">
            <a href="modify_team.php?id='.$team_id.'" class="quick"><img src="/Resources/img/ui_icons/crayon.png" alt="leader" class="btn_img">&nbsp;&nbsp;Modifier l\'équipe</a>
        </div>
        ';
    } else {
        echo '
        <div class="btn">
            <a href="modify_team.php?id='.$team_id.'" class="quick"><img src="/Resources/img/ui_icons/sign-out.png" alt="leader" class="btn_img">&nbsp;&nbsp;Quitter l\'équipe</a>
        </div>
        ';
    }

    ?>
    <div class="btn">
        <a href="index.php" class="quick"><img src="/Resources/img/ui_icons/back.png" alt="leader" class="btn_img">&nbsp;&nbsp;Retour</a>
    </div>
    </div>
</div>

<div class="container">
    <div class="hierarchy">
        <h3>Gérant</h3>
        
        <a target="_blank" href="/Sources/view_profile.php?id=<?=$team['leader']?>" class="member_item"><?=$leader_username?></a>
        <h3>Membres</h3>
        <div class="members">
        
        <?php

        $stmt = $pdo->prepare("SELECT * FROM TEAM_MEMBER WHERE id_team = :team_id");
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($members as $member) {
            $stmt = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
            $stmt->bindParam(':id', $member['id_user'], PDO::PARAM_INT);
            $stmt->execute();
            $username = $stmt->fetchColumn();

            if($member['id_user'] == $team['leader']) {
                continue;
            }

            echo '<a target="_blank" href="/Sources/view_profile.php?id='.$member['id_user'].'" class="member_item">'.$username.'</a>';

        }

        ?>
        </div>
    </div>
    <div class="next_activities">
        <div class="nx_header">
            <h3>Prochaine activitée</h3>
        </div>
        <div class="nx_container">

        <?php
        try{
            $stmt = $pdo->prepare("SELECT * FROM TEAM_ACTIVITY WHERE id_team = :team_id ORDER BY event_date ASC LIMIT 1");
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            $next_activity = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($next_activity) {
                $formatted_date = date('d/m/Y', strtotime($next_activity['event_date']));
                $next_activity['event_date'] = $formatted_date;
                echo '<div class="event_container">';
                echo '<p>'.$next_activity['name'].' &#x25CF; '.$next_activity['event_date'].'</p>';
                echo '<hr>';
                echo '<p>'.$next_activity['description'].'</p>';
                echo '</div>';
            } else {
                echo '
                <div class="msg">
                    <img src="/Resources/img/ui_icons/empty.png"  alt="empty">
                    <p class="txt">Aucune activité</p>
                </div>
                ';
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>

        </div>
    </div>
    <div class="activities">
        <div class="act_header">
            <h3>Actualité et évènements</h3>
            <a href="create_event.php?id=<?=$team_id?>" class="quick">+ Nouvelle activité / évènement</a>
            <hr class="line">
        </div>
        <div class="act_container">
        <?php
        $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM TEAM_ACTIVITY WHERE id_team = :team_id");
        $count_stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $count_stmt->execute();
        $count = $count_stmt->fetchColumn();

        if ($count > 0) {
            $stmt = $pdo->prepare("SELECT * FROM TEAM_ACTIVITY WHERE id_team = :team_id ORDER BY creation_date DESC");
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($activities as $activity) {
                $formatted_date = date('d/m/Y', strtotime($activity['event_date']));
                $activity['event_date'] = $formatted_date;
                echo '<div class="event_container">';
                echo '<p>'.$activity['name'].' &#x25CF; '.$activity['event_date'].'</p>';
                echo '<hr>';
                echo '<p>'.$activity['description'].'</p>';
                echo '</div>';
            }
        } else {
            echo '
            <div class="msg">
                <img src="/Resources/img/ui_icons/empty.png"  alt="empty">
                <p class="txt">Aucune activité</p>
            </div>
            ';
        }

        ?>
        </div>
    </div>
</div>

<?php
include_once 'footer.php';
?>