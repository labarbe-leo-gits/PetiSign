<?php

session_start();

//include_once 'database/database.php';
//$delete_session = $pdo->prepare("DELETE FROM SESSION WHERE ip_address = :ip_address");
//$delete_session->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
//$delete_session->execute();

include_once 'database/database.php';

if(isset($_SESSION['mail'])) {
    $user_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :mail");
    $user_id_stmt->bindParam(':mail', $_SESSION['mail']);
    $user_id_stmt->execute();
    $user_id = $user_id_stmt->fetchColumn();

    $update_last_activity = $pdo->prepare("UPDATE USER SET last_activity = :last_act WHERE id = :mail");
    $last_ac = NULL;
    $update_last_activity->bindParam(':last_act', $last_ac);
    $update_last_activity->bindParam(':mail', $user_id);
    $update_last_activity->execute();
}

function logout() {
    session_start();
    session_destroy();
    header('Location: login.php');
    exit();
}

logout();

?>