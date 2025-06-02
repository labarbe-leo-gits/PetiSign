<?php

include_once 'header.php';
include_once "../database/database.php";
include_once '../checker.php';

$id = $_GET['id'] ?? null;

if ($id === null || !is_numeric($id) || $id <= 0 || $id === '') {
    echo "No ID provided.";
    exit();
}

$check_newsletter_stmt = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE id = :id");
$check_newsletter_stmt->bindParam(':id', $id, PDO::PARAM_INT);
$check_newsletter_stmt->execute();
$newsletter_exists = $check_newsletter_stmt->fetchColumn();

if ($newsletter_exists == 0) {
    echo "Not found.";
    exit();
}


?>

<?php
                $stmt = $pdo->prepare("SELECT * FROM USER_CANDIDATE WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $team = $stmt->fetch(PDO::FETCH_ASSOC);

                $leader_id_to_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
                $leader_id_to_username->bindParam(':id', $team['id_user'], PDO::PARAM_INT);
                $leader_id_to_username->execute();
                $leader_username = $leader_id_to_username->fetchColumn();

                $typ_int_to_text = [
                    1 => 'Devenir bénévole',
                    2 => 'Demande de débanissement'
                ];

                $team['candidate_type'] = $typ_int_to_text[$team['candidate_type']] ?? 'Type inconnu';

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
        <h1 id="loginhigh" class="highlighted-text">Informations sur la demande</h1>
        <hr id="loginhr">
        
            <div class="entries">
                <div class="entries">
                    <p class="role_selector_text">&nbsp;Demandeur&nbsp;</p>
                    <div class="readonly-field"><p><?=$leader_username?></p></div>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Type</p>
                    <div class="readonly-field"><p><?php echo $team['candidate_type']?></p></div>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Contenu</p>
                    <div class="readonly-field"><p><?php echo nl2br($team['motivation'])?></p></div>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <p class="role_selector_text">Statut</p>
                    <div class="readonly-field"><p><?php echo htmlspecialchars($team['current_status'], ENT_QUOTES, 'UTF-8') ?></p></div>
                <div class="space"></div>
                <?php
                $formatted_date_timestamp = date('d/m/Y H:i', strtotime($team['date']));
                ?>
                <div class="entries">
                    <p class="role_selector_text">Date de réception</p>
                    <div class="readonly-field"><p><?php echo $formatted_date_timestamp ?> UTC</p></div>
                </div>
                <div class="space"></div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

        <button class="custom-button" onclick="window.location.href='candidates.php'" id="cancel_btn">Retour</button>
    </div>
</div>
</div>    