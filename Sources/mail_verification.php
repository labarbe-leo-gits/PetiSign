<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: register.php');
    exit();
}

include_once 'header.php';
include_once 'database/database.php';

$mail = htmlspecialchars(filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL));
$username = htmlspecialchars(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING));
$password = htmlspecialchars(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING));
$confpassword = htmlspecialchars(filter_input(INPUT_POST, 'confpassword', FILTER_SANITIZE_STRING));
$answer = htmlspecialchars(filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING));
$id = htmlspecialchars(filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT));

$filename = 'json/banned_username.json';
if (file_exists($filename)) {
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
} else {
    echo "File not found.";
    exit;
}

if (isset($data['banned_usernames'])) {
    $banned_usernames = $data['banned_usernames'];
    if (in_array($username, $banned_usernames)) {
        header('Location: register.php?error=BannedUsername&referer=mail_verification');
        exit;
    }
    
}


if (empty($mail) || empty($username) || empty($password) || empty($confpassword) || empty($answer) || empty($id)) {
    header('Location: register.php?error=EmptyFields&referer=mail_verification');
    exit();
}

if ($password != $confpassword) {
    header('Location: register.php?error=PasswordMismatch&referer=mail_verification');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM CAPTCHA WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$captcha = $stmt->fetch();

if($captcha['answer'] != $answer){
    header('Location: register.php?error=Captcha&referer=mail_verification');
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
require_once 'SendMailFunction.php';

$verification_code = rand(100000, 999999);
$_SESSION['verification_code'] = $verification_code;

$mail_sent = new PHPMailer(true);
EnvoieMail($mail_sent, $mail, $verification_code);

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/register.css">
<link rel="stylesheet" href="css/mail_verif.css">

<div class="login_form" id="register_form">
    <h1 id="loginhigh" class="highlighted-text">Vérification mail</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="Processus/register_user.php">
        <div class="entries">
            <div class="entries">
                <input name="verif" id="verif" type="number" required placeholder=" ">
                <label for="verif">Code de vérification</label>
            </div>
        </div>
        <button type="button" class="send custom-button loginbtn" onclick="window.location.reload(true);">Renvoyer un code</button>
        <button class="custom-button loginbtn" type="submit">S'inscrire</button>
    <input type="hidden" name="mail" value="<?php echo $mail; ?>">
    <input type="hidden" name="username" value="<?php echo $username; ?>">
    <input type="hidden" name="password" value="<?php echo $password; ?>">
    <input type="hidden" name="confpassword" value="<?php echo $confpassword; ?>">
    <input type="hidden" name="answer" value="<?php echo $answer; ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    </form>
</div>

<?php
include_once 'footer.php'
?>