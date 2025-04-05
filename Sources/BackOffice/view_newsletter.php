<?php

include_once 'header.php';
include_once "../database/database.php";

$id = $_GET['id'] ?? null;

if ($id === null || !is_numeric($id) || $id <= 0 || $id === '') {
    echo "No newsletter ID provided.";
    exit();
}

$check_newsletter_stmt = $pdo->prepare("SELECT COUNT(*) FROM NEWSLETTER WHERE id = :id");
$check_newsletter_stmt->bindParam(':id', $id, PDO::PARAM_INT);
$check_newsletter_stmt->execute();
$newsletter_exists = $check_newsletter_stmt->fetchColumn();

if ($newsletter_exists == 0) {
    echo "Newsletter not found.";
    exit();
}


?>

<link rel="stylesheet" href="../css/backoffice_view_newsletter.css">
<link rel="stylesheet" href="../css/backoffice_ban_user.css">
<link rel="stylesheet" href="../css/role_selector.css">

<div class="right_panel">
<div class="container">
    <div class="left_panel">
        <div class="container">
            <h1>Informations sur la Newsletter</h1>
            <div class="newsletter_content">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM NEWSLETTER WHERE id = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $newsletter = $stmt->fetch(PDO::FETCH_ASSOC);

                $full_date = $newsletter['date'];

                $format_date = new DateTime($newsletter['date']);
                $format_date = $format_date->format('d/m/Y');
                $newsletter['date'] = $format_date;

                $time = explode(" ", $full_date);
                $time = $time[1];

                // only get hours and minutes
                $time = explode(":", $time);
                $time = $time[0] . ":" . $time[1];


                if ($newsletter) {
                    echo "<p>Titre : " . htmlspecialchars($newsletter['title']) . "</p>";
                    echo "<p>Contenu : " . htmlspecialchars($newsletter['content']) . "</p>";
                    echo "<p>Envoyée le : " . htmlspecialchars($newsletter['date']) . " à " . $time . " UTC </p>";
                } else {
                    echo "Newsletter not found.";
                }

                $recipients_email_stmt = $pdo->prepare("SELECT email FROM USER WHERE id IN (SELECT id_user FROM ABONNEMENT WHERE id_newsletter = :id)");
                $recipients_email_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $recipients_email_stmt->execute();
                $recipients_email = $recipients_email_stmt->fetchAll(PDO::FETCH_ASSOC);

                if(count($recipients_email) == 0) {
                    echo "<p>Aucun destinataire pour cette newsletter</p>";
                } else {
                    echo "<h3>Destinataires:</h3>";
                }

                foreach($recipients_email as $recipient) {
                    echo "<p><strong>Dest:</strong> " . htmlspecialchars($recipient['email']) . "</p>";
                }

                ?>



            </div>
        </div>
    </div>
    </div>
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text">Informations sur la Newsletter</h1>
        <hr id="loginhr">
        
            <div class="entries">
                <div class="entries">
                    <p class="role_selector_text">Titre</p>
                    <div class="readonly-field"><p><?=$newsletter['title']?></p></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Contenu</p>
                    <div class="readonly-field"><p><?php echo nl2br($newsletter['content']) ?></p></div>
                </div>
                <div class="space"></div>

                <div class="entries">
                    <p class="role_selector_text">Destinataires</p>
                    <div class="readonly-field"><p>
                        <?php 
                        foreach($recipients_email as $recipient) {
                            echo "<a href='mailto:". htmlspecialchars($recipient['email']) ."'>" . htmlspecialchars($recipient['email']) ."</a></br>";
                        }

                        ?>
                    </p></div>
                </div>
                <div class="space"></div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">

        <button class="custom-button" onclick="window.location.href='users.php'" id="cancel_btn">Retour</button>
    </div>
</div>
</div>    