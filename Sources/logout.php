<?php

//include_once 'database/database.php';
//$delete_session = $pdo->prepare("DELETE FROM SESSION WHERE ip_address = :ip_address");
//$delete_session->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
//$delete_session->execute();

function logout() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();
}

logout();

?>