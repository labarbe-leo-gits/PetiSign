<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
require_once 'SendMailFunction.php';

if(isset($_GET['send_code']) && $_GET['send_code'] == 1) {
    if(isset($_POST['new']) && filter_var($_POST['new'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['new'];
        $random_generated_code = rand(100000, 999999);
        $_SESSION['pswd_form_change_email'] = $email;
        $_SESSION['pswd_form_change_code'] = $random_generated_code;
        
        $mail_sent = new PHPMailer(true);
        EnvoieMail($mail_sent, $email, $random_generated_code);
        
        header('Location: forgot_pswd.php');
        exit();
    } else {
        header('Location: reset.php?error=invalid_email');
        exit();
    }
}

if(isset($_GET['send_code']) && $_GET['send_code'] == 2) {
    if(isset($_POST['new']) && is_numeric($_POST['new']) && strlen($_POST['new']) == 6) {
        $code = $_POST['new'];
        if(isset($_SESSION['pswd_form_change_code']) && $code == $_SESSION['pswd_form_change_code']) {
            $_SESSION['pswd_code_validated'] = true;
            header('Location: forgot_pswd.php');
            exit();
        } else {
            header('Location: reset.php?error=invalid_code');
            exit();
        }
    } else {
        header('Location: reset.php?error=invalid_code');
        exit();
    }
}

if(!isset($_SESSION['pswd_form_change_email']) || !isset($_SESSION['pswd_form_change_code'])) {
    if(!isset($_SESSION['pswd_code_validated'])) {
        header('Location: reset.php?error=missing_data');
        exit();
    }
}

include_once 'header.php';
include_once 'database/database.php';
include_once 'checker.php';

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">

<?php

if(!isset($_SESSION['pswd_code_validated'])) {
    echo '
    <div class="login_form" id="login_form">
        <h1 id="loginhigh" class="highlighted-text">Changement de mot de passe</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="forgot_pswd.php?send_code=2">
            <div class="entries">
                <div class="entries">
                    <p>Entrez le code de vérification reçu par mail</p>
                </div>
                <div class="entries">
                    <input name="new" id="new" type="number" required placeholder=" ">
                    <label for="new">Code</label>
                </div>
                <button class="custom-button loginbtn" type="submit">Valider</button>
            </div>
        </form>
    </div>
    ';
} else {
    echo '
    <div class="login_form" id="login_form">
        <h1 id="loginhigh" class="highlighted-text">Changement de mot de passe</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/change_pswd.php?action_id=1">
            <div class="entries">
                <div class="entries">
                    <input name="new" id="new" type="password" required placeholder=" ">
                    <label for="new">Nouveau mot de passe</label>
                </div>
                <div class="space"></div>
                <div class="entries">
                    <input name="new_conf" id="new_conf" type="password" required placeholder=" ">
                    <label for="new_conf">Confirmer</label>
                </div>
            </div>
            <button class="custom-button loginbtn" type="submit">Changer le mot de passe</button>
        </form>
    </div>
    ';
}

include_once 'footer.php';
?>