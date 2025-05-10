<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if(isset($_GET['id']) && is_numeric($_GET['id'])){
        $stmt = $pdo->prepare("DELETE FROM REPORT WHERE id = :id");
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $stmt->execute();
        header('Location: ../moderation.php?code=RepDeleted');
        exit();
    }
    else{
        echo "Invalid ID";
        exit();
    }
}
else{
    header('Location: /Sources/error.php?code=403');
    exit();
}

?>