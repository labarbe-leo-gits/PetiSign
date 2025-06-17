<?php

session_start();

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}

include_once 'database/database.php';
use PHPMailer\PHPMailer\PHPMailer;
require_once 'SendNewsletterFunction.php';

$user_username = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
$user_username->bindParam(':mail', $_SESSION['mail']);
$user_username->execute();
$username = $user_username->fetchColumn();

$check_if_ban_exist_stmt = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = (SELECT id FROM USER WHERE email = :mail)");
$check_if_ban_exist_stmt->bindParam(':mail', $_SESSION['mail']);
$check_if_ban_exist_stmt->execute();
$check_if_ban_exist = $check_if_ban_exist_stmt->fetchColumn();

if($check_if_ban_exist == 0){
    header('Location: profile.php');
    exit();
}

$user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
$user_id_stmt->bindParam(':mail', $_SESSION['mail']);
$user_id_stmt->execute();
$user_id = $user_id_stmt->fetchColumn();

$get_ban = $pdo->prepare("SELECT * FROM BAN WHERE id_user = :id");
$get_ban->bindParam(':id', $user_id);
$get_ban->execute();
$ban = $get_ban->fetch(PDO::FETCH_ASSOC);

$admin_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
$admin_username->bindParam(':id', $ban['id_admin']);
$admin_username->execute();
$admin = $admin_username->fetchColumn();

$ban_reason_stmt = $pdo->prepare("SELECT reason FROM BAN WHERE id_user = :id");
$ban_reason_stmt->bindParam(':id', $user_id);
$ban_reason_stmt->execute();
$ban_reason = $ban_reason_stmt->fetchColumn();
$ban_reason = nl2br($ban_reason);

$expiration_stmt = $pdo->prepare("SELECT expiration FROM BAN WHERE id_user = :id");
$expiration_stmt->bindParam(':id', $user_id);
$expiration_stmt->execute();
$expiration = $expiration_stmt->fetchColumn();

$formated_date = date('d/m/Y', strtotime($expiration));
$expiration = $formated_date;

include_once 'special_header.php';

?>

    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $mail_obj = filter_input(INPUT_POST, 'obj', FILTER_SANITIZE_STRING);
        $mail_message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
        $mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
        $confirmed_mail = filter_var($mail, FILTER_VALIDATE_EMAIL);

        $get_username_from_mail_stmt = $pdo->prepare("SELECT username FROM USER WHERE email = :mail");
        $get_username_from_mail_stmt->bindParam(':mail', $confirmed_mail);
        $get_username_from_mail_stmt->execute();
        $username_from_mail = $get_username_from_mail_stmt->fetchColumn();

        if(!$username_from_mail){
            echo "<p class='error'>L'adresse e-mail fournie n'est pas valide ou n'existe pas dans notre base de données.</p>";
            exit();
        }

        $get_all_website_admin_stmt = $pdo->prepare("SELECT username, email FROM USER WHERE is_admin = 1 AND mail_notification = 1");
        $get_all_website_admin_stmt->execute();
        $all_website_admin = $get_all_website_admin_stmt->fetchAll(PDO::FETCH_ASSOC);

        $create_a_request_stmt = $pdo->prepare("INSERT INTO USER_CANDIDATE (id_user, motivation, current_status, candidate_type) VALUES ((SELECT id FROM USER WHERE email = :mail), :motivation, 'En Attente', 2)");
        $create_a_request_stmt->bindParam(':mail', $confirmed_mail);
        $create_a_request_stmt->bindParam(':motivation', $mail_message);
        $create_a_request_stmt->execute();

        $mail_sent = new PHPMailer(true);
        
        foreach($all_website_admin as $admin) {
            EnvoieMail($mail_sent, $admin['email'], $admin['username'], $mail_obj, "$username_from_mail ($confirmed_mail) a effectué une demande de contact. Voici le corps de son message :<br /><br /> $mail_message", "administrateur du site");
        }

        echo "<p class='success'>Votre message a été envoyé avec succès !</p>";

        $_POST = array();
        header("Location: ban.php");
        exit();

    }

    ?>

<div class="ban_container">
    <h2>Bonjour <?= $username ?>,</h2>
    <p class="header_text" id="first">Nous sommes désolés de vous informer que votre compte a été banni.</p>
    <p class="header_text">Voici les détails de votre bannissement :</p>

    <div class="details">
        <div class="details_item">
            <h3>Administrateur à l'origine du bannissement :</h3>
            <p><?= $admin ?></p>
        </div>
        <div class="details_item ban_reason_container">
            <h3>Raison du bannissement :</h3>
            <p><?= $ban_reason ?></p>
        </div>
        <div class="details_item">
            <h3>Date de fin du bannissement :</h3>
            <p><?= $expiration ?></p>
    </div>
</div>
    <p class="header_text" id="second">Nous vous prions de bien vouloir respecter les règles de la plateforme à l'avenir.</p>
    <p class="header_text">Si vous pensez qu'il s'agit d'une erreur, n'hésitez pas à nous contacter.</p>
    <p class="header_text">Nous vous remercions de votre compréhension.</p>
    <p class="header_text" id="signature_text">Cordialement,</p>
    <p class="header_text" id="last">L'équipe de PétiSign</p>

    <button type="button" class="custom-button loginbtn" onclick="window.location.href='logout.php';">Déconnexion</button>
</div>

</div>

<link rel="stylesheet" href="css/backoffice_ban_user.css">

<?php
$check_if_already_submitted_stmt = $pdo->prepare("SELECT COUNT(*) FROM USER_CANDIDATE WHERE id_user = (SELECT id FROM USER WHERE email = :mail) AND candidate_type = 2 AND current_status = 'En Attente'");
$check_if_already_submitted_stmt->bindParam(':mail', $_SESSION['mail']);
$check_if_already_submitted_stmt->execute();
$check_if_already_submitted = $check_if_already_submitted_stmt->fetchColumn();

// echo "<script>alert('".$check_if_already_submitted."');</script>";

?>
<?php if($check_if_already_submitted <= 0):?>
<div class="ban_container">
    <h2>Contactez-nous</h2>
    <form action="ban.php" method="POST">
        <div class="container">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" " value="<?=$_SESSION['mail']?>" class="form-input">
                <label for="mail">Adresse e-mail</label>
            </div>
            <div class="entries">
                <input name="obj" id="obj" type="text" required placeholder=" " value="Demande de débanissement" class="form-input">
                <label for="obj">Objet</label>
            </div>
            <div class="area">
            <p class="role_selector_text">Message</p>
                <textarea required name="message" id="message" maxlength="1200" placeholder="Écrivez votre message ici ..."></textarea>    
            </div>
            <button type="submit" class="custom-button loginbtn">Envoyer</button>
        </div>
    </form>
</div>
<?php else: ?>
<div class="ban_container">
    <div class="message">
    <img src="/Resources/img/ui_icons/validate.png" alt="Success Icon" class="success_icon">
    <p class="header_text">Votre demande a été prise en compte et sera traitée dans les plus brefs délais.</p>
    </div>
</div>
<?php endif; ?>

<?php
include_once 'footer.php';
?>