<?php
include_once 'header.php';

if(isset($_GET['error'])){
    $json_file = file_get_contents('json/error_register.json');
    $error_manager = json_decode($json_file, true);
    if(array_key_exists($_GET['error'], $error_manager)){
        $insertVal = $_GET['error'];
    }
}

$error_details = $error_manager[$insertVal];

if(isset($_SESSION['mail'])){
    //header('Location: profile.php');
    $get_id_from_email = $pdo->prepare("SELECT id FROM USER WHERE email = :email");
    $get_id_from_email->bindParam(':email', $_SESSION['mail']);
    $get_id_from_email->execute();
    $user_id = $get_id_from_email->fetchColumn();
    echo "<script>window.location.href = 'view_profile.php?id=". $user_id ."';</script>";
    exit();
}

?>

<link rel="stylesheet" href="css/login_register.css">
<link rel="stylesheet" href="css/login.css">

<?php

if(isset($_GET['error'])){
    echo '
    <div class="error">
        <div class="error_message">
            <p class="error_text">' . $error_details .'</p>
        </div>
    </div>
    ';
}
?>

<div class="login_form" id="login_form">
    <h1 id="loginhigh" class="highlighted-text">Connexion</h1>
    <hr id="loginhr">
    <form method="post" class="login" action="Processus/login.php">
        <div class="entries">
            <div class="entries">
                <input name="mail" id="mail" type="email" required placeholder=" " class="form-input">
                <label for="mail">Adresse e-mail</label>
            </div>
            <div class="space"></div>
            <div class="entries">
                <input name="password" id="password" type="password" required placeholder=" " class="form-input">
                <label for="password">Mot de passe</label>
            </div>
        </div>
        <button class="custom-button loginbtn" type="submit">Se Connecter</button>
    </form>
    <hr id="loginhr2">
    <p class="smallTxt">Pas encore membre ? <a href="register.php">S'inscrire</a> &#x25CF; <a href="reset.php">Mot de passe oublié ?</a></p>
</div>

<?php
include_once 'footer.php'
?>