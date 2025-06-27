<?php

use PHPMailer\PHPMailer\PHPMailer;
include_once '../../loading.php';
include_once '../../database/database.php';
include_once 'security.php';
include_once '../../send_notif.php';

if ($id_benevole != 0) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {

            $activity_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $event_date = filter_input(INPUT_POST, 'event_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            //$description = nl2br(html_entity_decode($description, ENT_QUOTES, 'UTF-8'));
            $nb_part = filter_input(INPUT_POST, 'nb_part', FILTER_SANITIZE_NUMBER_INT);
            $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $postal_code = filter_input(INPUT_POST, 'pcode', FILTER_SANITIZE_NUMBER_INT);
            $road = filter_input(INPUT_POST, 'road', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $num = filter_input(INPUT_POST, 'num', FILTER_SANITIZE_NUMBER_INT);
            $activity_id = filter_input(INPUT_POST, 'activity_id', FILTER_SANITIZE_NUMBER_INT);

            $description = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $description);
            $activity_name = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $activity_name);
            $road = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $road);
            $city = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F000}-\x{1F02F}]|[\x{1F0A0}-\x{1F0FF}]|[\x{E000}-\x{F8FF}]|[\x{FE00}-\x{FE0F}]|[\x{1F200}-\x{1F2FF}]/u', '', $city);

            if(mb_strlen($description) > 150 || mb_strlen($postal_code) > 5 || mb_strlen($road) > 60 || mb_strlen($city) > 30 || mb_strlen($activity_name) > 60) {
                echo "<script>window.location.href = '../modify_activity_form.php?id=". $activity_id ."&code=len';</script>";
                exit();
            }

            if(empty($activity_id) || !is_numeric($activity_id)) {
                echo "<script>window.location.href = '/Sources/error.php?code=400';</script>";
                exit();
            }

            if (empty($activity_name) || empty($event_date) || empty($description) || empty($city) || empty($postal_code) || empty($road) || empty($num)) {
                echo "<script>window.location.href = '../modify_activity_form.php?id=". $activity_id ."&code=mty';</script>";
                exit();
            }

            $today = date('Y-m-d');
            if ($event_date < $today) {
                echo "<script>window.location.href = '../modify_activity_form.php?id=". $activity_id ."&code=past';</script>";
                exit();
            }

            $old_participants_num = $pdo->prepare("SELECT max_participants FROM TEAM_ACTIVITY WHERE id = :id");
            $old_participants_num->bindParam(':id', $activity_id, PDO::PARAM_INT);
            $old_participants_num->execute();
            $old_participants_num = $old_participants_num->fetchColumn();

            if(($old_participants_num == false && $nb_part > 0) || ($old_participants_num < $nb_part && $nb_part > 0)) {
                $update_max_participants = $pdo->prepare("UPDATE TEAM_ACTIVITY SET max_participants = :max_participants WHERE id = :id");
                $update_max_participants->bindParam(':max_participants', $nb_part, PDO::PARAM_INT);
                $update_max_participants->bindParam(':id', $activity_id, PDO::PARAM_INT);
                $update_max_participants->execute();
            }

            if($nb_part < $old_participants_num){
                $count_inscriptions = $pdo->prepare("SELECT COUNT(*) FROM ACTIVITY_INSCRIPTION WHERE id_activity = :activity_id");
                $count_inscriptions->bindParam(':activity_id', $activity_id, PDO::PARAM_INT);
                $count_inscriptions->execute();
                $inscriptions_count = $count_inscriptions->fetchColumn();

                if($inscriptions_count > $nb_part) {
                    echo  "<script>window.location.href = '../modify_activity_form.php?id=". $activity_id ."&code=toomanyinscrit';</script>";
                    exit();
                }

                $update_max_participants = $pdo->prepare("UPDATE TEAM_ACTIVITY SET max_participants = :max_participants WHERE id = :id");
                $update_max_participants->bindParam(':max_participants', $nb_part, PDO::PARAM_INT);
                $update_max_participants->bindParam(':id', $activity_id, PDO::PARAM_INT);
                $update_max_participants->execute();

            }

            $select_initial_adress_components_stmt = $pdo->prepare("SELECT city, postal_code, rue, num FROM TEAM_ACTIVITY WHERE id = :id");
            $select_initial_adress_components_stmt->bindParam(':id', $activity_id, PDO::PARAM_INT);
            $select_initial_adress_components_stmt->execute();
            $initial_adress_components = $select_initial_adress_components_stmt->fetch(PDO::FETCH_ASSOC);

            $select_initial_date_stmt = $pdo->prepare("SELECT event_date FROM TEAM_ACTIVITY WHERE id = :id");
            $select_initial_date_stmt->bindParam(':id', $activity_id, PDO::PARAM_INT);
            $select_initial_date_stmt->execute();
            $initial_date = $select_initial_date_stmt->fetchColumn();

            $adress_changed = false;
            $date_changed = false;

            if ($initial_adress_components['city'] !== $city || $initial_adress_components['postal_code'] !== $postal_code || $initial_adress_components['rue'] !== $road || $initial_adress_components['num'] !== $num) {
                $adress_changed = true;
            }

            if ($initial_date !== $event_date) {
                $date_changed = true;
            }

            $update_stmt  = $pdo->prepare("UPDATE TEAM_ACTIVITY SET name = :name, event_date = :event_date, description = :description, city = :city, postal_code = :postal_code, rue = :road, num = :num WHERE id = :id");
            $update_stmt->bindParam(':name', $activity_name, PDO::PARAM_STR);
            $update_stmt->bindParam(':event_date', $event_date, PDO::PARAM_STR);
            $update_stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $update_stmt->bindParam(':city', $city, PDO::PARAM_STR);
            $update_stmt->bindParam(':postal_code', $postal_code, PDO::PARAM_INT);
            $update_stmt->bindParam(':road', $road, PDO::PARAM_STR);
            $update_stmt->bindParam(':num', $num, PDO::PARAM_INT);
            $update_stmt->bindParam(':id', $activity_id, PDO::PARAM_INT);
            $update_stmt->execute();

            $mail_content = "L'activité <strong>" . htmlspecialchars($activity_name) . "</strong> a été modifiée !<br>";

            if ($adress_changed) {
                $mail_content .= "Voici la nouvelle adresse : <address>" . htmlspecialchars($num) . " " . htmlspecialchars($road) . ", " . htmlspecialchars($postal_code) . " " . htmlspecialchars($city) . "</address><br>";
            }

            if ($date_changed) {
                $event_date = date('d/m/Y', strtotime($event_date));
                $mail_content .= "La date de l'activité a été modifiée ! La nouvelle date est la suivante : <strong>" . htmlspecialchars($event_date) . "</strong>.<br>";
            }

            $mail_content .= "<br>Merci de votre interêt et à bientôt !<br>
                L'équipe de Petisign";

            $get_all_users_that_are_inscribed_stmt = $pdo->prepare("SELECT id_user FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id_activity");
            $get_all_users_that_are_inscribed_stmt->bindParam(':id_activity', $activity_id, PDO::PARAM_INT);
            $get_all_users_that_are_inscribed_stmt->execute();
            $users = $get_all_users_that_are_inscribed_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($users as $user) {
                $user_id = $user['id_user'];
                
                $user_username = $pdo->prepare("SELECT username, email FROM USER WHERE id = :id_user AND mail_notification = 1");
                $user_username->bindParam(':id_user', $user_id, PDO::PARAM_INT);
                $user_username->execute();
                $username = $user_username->fetch(PDO::FETCH_ASSOC);

                $mail_sent = new PHPMailer(true);
                EnvoieMail($mail_sent, $username['email'], $username['username'], "Modification d'un évènement", $mail_content);
                
            }
            
            echo "Success";
            echo "<script>window.location.href = '../view_activity.php?id=". $activity_id ."';</script>";
            exit();


        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>