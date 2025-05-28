<?php

include_once 'security.php';
include_once '../../database/database.php';

if($is_admin != 0){

    $get_user_ban_id = $pdo->prepare("SELECT id FROM BAN WHERE id_user = :user_id");
    $get_user_ban_id->bindParam(':user_id', $_GET['id']);
    $get_user_ban_id->execute();
    $ban_id = $get_user_ban_id->fetchColumn();

    if($ban_id){

        $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id_user = :id");
        $delete_ban->bindParam(':id', $_GET['id']);
        $delete_ban->execute();

        header("Location: ../users.php?success=UnbanSuccess&referer=admin");
        exit;
    }
}else{
    header("Location: /Sources/error.php?code=403");
    exit;
}


?>