<?php

include_once 'header.php';
include_once "../database/database.php";
include_once '../checker.php';

$id = $_GET['id'] ?? null;

if ($id === null || !is_numeric($id) || $id <= 0 || $id === '') {
    echo "No newsletter ID provided.";
    exit();
}

$check_newsletter_stmt = $pdo->prepare("SELECT COUNT(*) FROM TEAM WHERE id = :id");
$check_newsletter_stmt->bindParam(':id', $id, PDO::PARAM_INT);
$check_newsletter_stmt->execute();
$newsletter_exists = $check_newsletter_stmt->fetchColumn();

if ($newsletter_exists == 0) {
    echo "Newsletter not found.";
    exit();
}


?>

<?php
                $stmt = $pdo->prepare("SELECT * FROM TEAM WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $team = $stmt->fetch(PDO::FETCH_ASSOC);

                $leader_id_to_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                $leader_id_to_username->bindParam(':id', $team['leader'], PDO::PARAM_INT);
                $leader_id_to_username->execute();
                $leader_username = $leader_id_to_username->fetchColumn();

                $recipients_email_stmt = $pdo->prepare("SELECT id, username FROM USER WHERE id IN (SELECT id_user FROM TEAM_MEMBER WHERE id_team = :id)");
                $recipients_email_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $recipients_email_stmt->execute();
                $recipients_email = $recipients_email_stmt->fetchAll(PDO::FETCH_ASSOC);

                $activity_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM TEAM_ACTIVITY WHERE id_team = :id");
                $activity_count_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $activity_count_stmt->execute();
                $activity_count = $activity_count_stmt->fetchColumn();


                ?>

<link rel="stylesheet" href="../css/backoffice_view_newsletter.css">
<link rel="stylesheet" href="../css/backoffice_ban_user.css">
<link rel="stylesheet" href="../css/role_selector.css">

<div class="right_panel">
<div class="container">
    <div class="left_panel">
    </div>
    </div>
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Informations sur l'équipe</h1>
        <hr id="loginhr">
        
            <div class="entries">
                <div class="entries">
                    <p class="role_selector_text">Nom</p>
                    <div class="readonly-field"><p><?=$team['name']?></p></div>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Description</p>
                    <div class="readonly-field"><p><?php echo nl2br($team['description'])?></p></div>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Secteur</p>
                    <div class="readonly-field"><p><a target="blank_" <?php if($team['sector'] != "Aucun secteur renseigné"){
                        echo "href='https://www.google.com/maps?q=" . $team['sector'] ."'";
                    } ?>><?=$team['sector']?></a></p></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Leader</p>
                    <div class="readonly-field"><p><a href="/Sources/view_profile.php?id=<?=$team['leader']?>" target="blank_"><?=$leader_username?></a></p></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Membres</p>
                    <div class="readonly-field"><p>
                        <?php 
                        foreach($recipients_email as $recipient) {
                            if($recipient['id'] == $team['leader']) {
                                continue;
                            }
                            echo "<a href='/Sources/view_profile.php?id=". $recipient['id'] ."' target='blank_'>" . htmlspecialchars($recipient['username']) ."</a></br>";
                        }

                        ?>
                    </p></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Nombre d'activité</p>
                    <div class="readonly-field"><p><?php echo $activity_count ?></p></div>
                </div>
                <div class="space"></div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

        <button class="custom-button" onclick="window.location.href='teams.php'" id="cancel_btn">Retour</button>
    </div>
</div>
</div>    