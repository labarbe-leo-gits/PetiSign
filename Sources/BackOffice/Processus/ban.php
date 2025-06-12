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

        $check_if_any_reports_exists_stmt = $pdo->prepare("SELECT COUNT(*) FROM REPORT WHERE current_status = 'OPEN' AND report_type = 1 AND id_target = :id_user");
        $check_if_any_reports_exists_stmt->bindParam(':id_user', $user_id);
        $check_if_any_reports_exists_stmt->execute();
        $any_reports_exists = $check_if_any_reports_exists_stmt->fetchColumn();

        if($any_reports_exists > 0){
            $close_the_reports_stmt = $pdo->prepare("UPDATE REPORT SET current_status = 'CLOSED' WHERE id_target = :id_user AND current_status = 'OPEN' AND report_type = 1");
            $close_the_reports_stmt->bindParam(':id_user', $user_id);
            $close_the_reports_stmt->execute();
        }

        if($any_reports_exists > 0){
            $update_report_status = $pdo->prepare("UPDATE REPORT SET current_status = 'CLOSED' WHERE id_user = :id_user AND current_status = 'OPEN'");
            $update_report_status->bindParam(':id_user', $user_id);
            $update_report_status->execute();
        }

        echo "User banned successfully.";
        header("Location: ../users.php?success=BanSuccess&referer=admin");
        exit();
    } else{
        if(isset($_GET['user_id']) && isset($_GET['admin_key'])){
            $admin_id_stmt = $pdo->prepare("SELECT id FROM USER WHERE email = :email");
            $admin_id_stmt->bindParam(':email', $_SESSION['mail']);
            $admin_id_stmt->execute();
            $admin_id = $admin_id_stmt->fetchColumn();

            $user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
            $today = date("Y-m-d");
            $seven_days_later = date("Y-m-d", strtotime("+7 days"));
            $ban_reason = "Un signalement a été reçu pour ce compte et ce dernier a été jugé valide par l'administrateur en charge de votre bannissement.";

            $report_id = filter_input(INPUT_GET, 'report_id', FILTER_SANITIZE_NUMBER_INT);

            $check_if_already_banned = $pdo->prepare("SELECT COUNT(*) FROM BAN WHERE id_user = :id_user");
            $check_if_already_banned->bindParam(':id_user', $user_id);
            $check_if_already_banned->execute();
            $already_banned = $check_if_already_banned->fetchColumn();

            if($already_banned > 0){
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit();
            }

            $stmt = $pdo->prepare("INSERT INTO BAN (id_user, id_admin, reason, expiration) VALUES (:id_user, :id_admin, :reason, :expiration_date)");
            $stmt->bindParam(':id_user', $user_id);
            $stmt->bindParam(':id_admin', $admin_id);
            $stmt->bindParam(':reason', $ban_reason);
            $stmt->bindParam(':expiration_date', $seven_days_later);
            $stmt->execute();

            $update_report_status = $pdo->prepare("UPDATE REPORT SET current_status = 'CLOSED' WHERE id = :report_id");
            $update_report_status->bindParam(':report_id', $report_id);
            $update_report_status->execute();

            header("Location: " . $_SERVER['HTTP_REFERER'] . "?success=BanSuccess&referer=admin");
            exit();


        }
        else{
            header("Location: ". $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}

?>