<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    $delete_stmt = $pdo->prepare("DELETE FROM REPORT WHERE current_status = 'CLOSED'");
    $delete_stmt->execute();
    if($delete_stmt){
        header('Location: ../moderation.php?code=DelSuccess');
    }
    else {
        header('Location: ../moderation.php?code=DelError');
    }
    
} else {
    header('Location: /Sources/error.php?code=403');
    exit();

}
?>