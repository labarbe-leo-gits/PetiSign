<?php
include_once 'header.php';
include_once 'database/database.php';
include_once 'Processus/write_logs.php';
include_once 'checker.php';

if(!isset($_SESSION['mail'])){
    header('Location: login.php');
    exit();
}


if(isset($_GET['error'])){
    $json_file = file_get_contents('json/error_register.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['error'], $error_manager)){
        $insertVal = $_GET['error'];
    }
}

$error_details = $error_manager[$insertVal];

$mail = $_SESSION['mail'];

include_once 'Processus/sessionlocked_security.php';

$user_stmt = $pdo->prepare('SELECT username FROM USER WHERE email = :mail');
$user_stmt->bindParam(':mail', $mail);
$user_stmt->execute();
$user = $user_stmt->fetchColumn();

$user_ip = $_SERVER['REMOTE_ADDR'];

write_logs('logs/log.txt', 'PROF1L', $user, $user_ip, 'Visite de la page "Profile"');

try{
    $get_user = $pdo->prepare('SELECT username FROM USER WHERE email = :mail');
    $get_user->bindParam(':mail', $mail);
    $get_user->execute();
    $user = $get_user->fetchColumn();

    $get_description = $pdo->prepare('SELECT description FROM USER WHERE email = :mail');
    $get_description->bindParam(':mail', $mail);
    $get_description->execute();
    $description = $get_description->fetchColumn();

    $outputed_description = nl2br($description);

    $get_newsletter_statut = $pdo->prepare('SELECT newsletter FROM USER WHERE email = :mail');
    $get_newsletter_statut->bindParam(':mail', $mail);
    $get_newsletter_statut->execute();
    $newsletter_statut = $get_newsletter_statut->fetchColumn();

    $get_mail_notification_statut = $pdo->prepare('SELECT mail_notification FROM USER WHERE email = :mail');
    $get_mail_notification_statut->bindParam(':mail', $mail);
    $get_mail_notification_statut->execute();
    $mail_notification_statut = $get_mail_notification_statut->fetchColumn();

    $get_birthdate = $pdo->prepare('SELECT birthdate FROM USER WHERE email = :mail');
    $get_birthdate->bindParam(':mail', $mail);
    $get_birthdate->execute();
    $birthdate = $get_birthdate->fetchColumn();

    $get_gender = $pdo->prepare('SELECT gender FROM USER WHERE email = :mail');
    $get_gender->bindParam(':mail', $mail);
    $get_gender->execute();
    $gender = $get_gender->fetchColumn();

    $get_avatar_hat = $pdo->prepare('SELECT avatar_hat FROM USER WHERE email = :mail');
    $get_avatar_hat->bindParam(':mail', $mail);
    $get_avatar_hat->execute();
    $avatar_hat = $get_avatar_hat->fetchColumn();

    $get_avatar_eyes = $pdo->prepare('SELECT avatar_eyes FROM USER WHERE email = :mail');
    $get_avatar_eyes->bindParam(':mail', $mail);
    $get_avatar_eyes->execute();
    $avatar_eyes = $get_avatar_eyes->fetchColumn();

    $get_avatar_mouth = $pdo->prepare('SELECT avatar_mouth FROM USER WHERE email = :mail');
    $get_avatar_mouth->bindParam(':mail', $mail);
    $get_avatar_mouth->execute();
    $avatar_mouth = $get_avatar_mouth->fetchColumn();

    $get_avatar_skin = $pdo->prepare('SELECT avatar_skin FROM USER WHERE email = :mail');
    $get_avatar_skin->bindParam(':mail', $mail);
    $get_avatar_skin->execute();
    $avatar_skin = $get_avatar_skin->fetchColumn();

    $get_avatar_hat_color = $pdo->prepare('SELECT avatar_hat_color FROM USER WHERE email = :mail');
    $get_avatar_hat_color->bindParam(':mail', $mail);
    $get_avatar_hat_color->execute();
    $avatar_hat_color = $get_avatar_hat_color->fetchColumn();

    $get_avatar_eyes_color = $pdo->prepare('SELECT avatar_eyes_color FROM USER WHERE email = :mail');
    $get_avatar_eyes_color->bindParam(':mail', $mail);
    $get_avatar_eyes_color->execute();
    $avatar_eyes_color = $get_avatar_eyes_color->fetchColumn();

    $get_avatar_mouth_color = $pdo->prepare('SELECT avatar_mouth_color FROM USER WHERE email = :mail');
    $get_avatar_mouth_color->bindParam(':mail', $mail);
    $get_avatar_mouth_color->execute();
    $avatar_mouth_color = $get_avatar_mouth_color->fetchColumn();

    $get_avatar_skin_color = $pdo->prepare('SELECT avatar_skin_color FROM USER WHERE email = :mail');
    $get_avatar_skin_color->bindParam(':mail', $mail);
    $get_avatar_skin_color->execute();
    $avatar_skin_color = $get_avatar_skin_color->fetchColumn();

}catch (PDOException $e){
    echo 'Erreur : '.$e->getMessage();
}

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/profile.css">

<script src="js/profile.js" defer></script>
<body class="dark-mode profile-page"> 
<div class="profile-container">
    <div class="profil_gauche">
        <h2 id="loginhigh" class="highlighted-text">Aperçu</h2>
        <div class="space"></div>
            <div class="avatar">
                <img class="skin" src="../Resources/avatar/skin/skin<?=$avatar_skin?>c<?=$avatar_skin_color?>.png" alt="">
                <img src="../Resources/avatar/hat/hat<?=$avatar_hat?>c<?=$avatar_hat_color?>.png" class="hat" alt="Hat" id="hat">
                <img src="../Resources/avatar/eyes/eye<?=$avatar_eyes?>c<?=$avatar_eyes_color?>.png" class="eyes" alt="Eyes" id="eyes">
                <img src="../Resources/avatar/mouth/smile<?=$avatar_mouth?>c<?=$avatar_mouth_color?>.png" class="mouth" alt="Mouth" id="mouth">
            </div>
        <h2 id="nomdp"><?=$user?></h2>
        <p id="description_profile"><?=$outputed_description?></p>
        <button class="custom-button dl_btn" onclick="window.location.href='Processus/download_my_data.php'">
    Télécharger mes données
</button>


    </div>
    <div class="login_form" id="register_form">
        <?php
            if(isset($_GET['error'])){
                echo '
                <div class="error">
                    <div class="error_message">
                        <p class="txterror">' . $error_details .'</p>
                    </div>
                </div>
                ';
            }
        ?>
        <h1 id="loginhigh" class="highlighted-text">Mes informations</h1>
        <hr id="loginhr">
        <form method="post" class="login" id="profile_form" action="Processus/profile_updater.php">
            <div class="entries">
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="mail" id="mail" type="email" placeholder=" " value="<?= htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="mail">Adresse mail</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="username" id="username" type="text" placeholder=" " value="<?= htmlspecialchars($user, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="username">Nom d'utilisateur</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <select class="editable" disabled name="gender" id="gender" onchange="updateLabel(this)">
                        <option value="Homme" <?php if ($gender == "Homme") echo 'selected'; ?>>Homme</option>
                        <option value="Femme" <?php if ($gender == "Femme") echo 'selected'; ?>>Femme</option>
                        <option value="Autre" <?php if ($gender == "Autre") echo 'selected'; ?>>Autre</option>
                        <option value="Non Renseigné" <?php if ($gender == "Non Renseigné") echo 'selected'; ?>>Non Renseigné</option>
                    </select>
                    <label for="gender" class="labels">Genre</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input class="editable" disabled name="anniv" id="anniv" type="date" value="<?= htmlspecialchars($birthdate, ENT_QUOTES, 'UTF-8') ?>" required>
                    <label for="anniv">Date de naissance</label>
                </div>

                <div class="space"></div>
                <div class="entries">
                    <textarea class="editable" disabled name="description" id="description"><?=$description?></textarea>
                    <label for="description">Description</label>
                </div>
                <div class="space"></div>
                <div class="entries checkbox-container">
                    <input type="hidden" name="newsletter_value" id="newsletter_value" value="<?= $newsletter_statut == 1 ? '1' : '0'; ?>">
                    <input class="editable" type="checkbox" name="newsletter" id="newsletter" <?php if ($newsletter_statut == 1) echo 'checked'; ?> value="1" class="checkbox" disabled>
                    <label for="newsletter" class="checkbox-label" id="news_label">Recevoir la newsletter</label>
                </div>
                <div class="entries checkbox-container">
                    <input type="hidden" name="mails_notif_value" id="mails_notif_value" value="<?= $mail_notification_statut == 1 ? '1' : '0'; ?>">
                    <input class="editable second_check" type="checkbox" name="mails_notif" id="mails_notif" <?php if ($mail_notification_statut == 1) echo 'checked'; ?> value="1" class="checkbox" disabled>
                    <label for="mails_notif" class="checkbox-label" id="mails_label">Notifications Mails</label>
                </div>
            </div>

            <hr id="loginhr_">
            
            <button type="button" id="loginbtn" class="custom-button loginbtn">Modifier</button>
            <button type="button" onclick="window.location.href='password_form.php'" class="custom-button loginbtn" id="pswd_btn">Changer mon mot de passe</button>
            <button type="button" onclick="window.location.href='modify_avatar.php'" class="custom-button loginbtn" id="avatar_btn">Modifier mon avatar</button>
            <button type="submit" id="save_btn" class="custom-button loginbtn">Enregistrer</button>
        </form>
        <hr id="btn_hr">
        <button type="button" class="custom-button loginbtn" onclick="window.location.href='logout.php';">Déconnexion</button>
    </div>
</div>
</body>

<?php
include_once 'footer.php';
?>