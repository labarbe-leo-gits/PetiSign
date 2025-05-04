<?php
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';

if($is_admin != 0){
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        $user_id = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_NUMBER_INT);
        $admin_id = filter_input(INPUT_POST, 'admin', FILTER_SANITIZE_NUMBER_INT);
        $ban_reason = htmlspecialchars(filter_input(INPUT_POST, 'ban_reason', FILTER_SANITIZE_STRING));
        $expiration_date = preg_replace("([^0-9/])", "", $_POST['ban_expiration']);

        if(empty($ban_reason) || empty($expiration_date) || empty($user_id) || empty($admin_id)){
            echo $ban_reason . " " . $expiration_date . " " . $user_id . " " . $admin_id;
            exit();
        }

        if(!$ban_reason || !$expiration_date || !$user_id || !$admin_id){
            echo "Invalid input.";
            exit();
        }

        $check_if_already_banned = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :id_user");
        $check_if_already_banned->bindParam(':id_user', $user_id);
        $check_if_already_banned->execute();
        $already_banned = $check_if_already_banned->fetchColumn();

        if($already_banned > 0){
            header("Location: ../users.php");
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO BAN (id_user, id_admin, reason, expiration) VALUES (:id_user, :id_admin, :reason, :expiration_date)");
        $stmt->bindParam(':id_user', $user_id);
        $stmt->bindParam(':id_admin', $admin_id);
        $stmt->bindParam(':reason', $ban_reason);
        $stmt->bindParam(':expiration_date', $expiration_date);
        $stmt->execute();

        echo "User banned successfully.";
        header("Location: ../users.php");
        exit();
    } else {
        echo "Invalid request method.";
    }
}

?>