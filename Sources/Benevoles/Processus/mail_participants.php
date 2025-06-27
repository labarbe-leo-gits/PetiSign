<?php
use PHPMailer\PHPMailer\PHPMailer;
$database_path = "/var/www/html/Sources/database/database.php";
$mail_notification_path = "/var/www/html/Sources/send_notif.php";

include_once($database_path);
include_once($mail_notification_path);

$envfile = "/var/www/html/Sources/BackOffice/Processus/key.env";
$env = parse_ini_file($envfile);
$key = $env["CrontabKey"];

$logFile = "/var/www/html/Sources/logs/log.txt";

if (!is_writable(dirname($logFile))) {
    echo "Error: Log directory is not writable. Please check permissions.\n";
    exit();
}


if($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['key']) || $_GET['key'] !== $key) {
    header('Location: /Sources/error.php?code=403');
    exit();
}

try {

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        echo "Error: Database connection not available.\n";
        exit();
    }

    $select_all_activitues_today = $pdo->prepare("SELECT id, name FROM TEAM_ACTIVITY WHERE event_date = CURDATE()");
    $select_all_activitues_today->execute();
    $all_activities_today = $select_all_activitues_today->fetchAll(PDO::FETCH_ASSOC);

    foreach ($all_activities_today as $activity) {

        $select_corresponding_team = $pdo->prepare("SELECT id_team FROM TEAM_ACTIVITY WHERE id = :id_activity");
        $select_corresponding_team->bindParam(':id_activity', $activity['id'], PDO::PARAM_INT);
        $select_corresponding_team->execute();
        $team = $select_corresponding_team->fetchColumn();

        $team_id_to_name = $pdo->prepare("SELECT name FROM TEAM WHERE id = :id_team");
        $team_id_to_name->bindParam(':id_team', $team, PDO::PARAM_INT);
        $team_id_to_name->execute();
        $team_name = $team_id_to_name->fetchColumn();

        $get_all_users_that_are_currently_inscribed = $pdo->prepare("SELECT id_user FROM ACTIVITY_INSCRIPTION WHERE id_activity = :id_activity");
        $get_all_users_that_are_currently_inscribed->bindParam(':id_activity', $activity['id'], PDO::PARAM_INT);
        $get_all_users_that_are_currently_inscribed->execute();
        $all_users_that_are_currently_inscribed = $get_all_users_that_are_currently_inscribed->fetchAll(PDO::FETCH_ASSOC);

        foreach ($all_users_that_are_currently_inscribed as $user) {
            $get_user_email = $pdo->prepare("SELECT email FROM USER WHERE id = :id_user AND mail_notification = 1");
            $get_user_email->bindParam(':id_user', $user['id_user'], PDO::PARAM_INT);
            $get_user_email->execute();
            $user_email = $get_user_email->fetch(PDO::FETCH_ASSOC);

            echo "Sending email to user ID: " . $user['id_user'] . " for activity ID: " . $activity['id'] . "\n";
            echo "email: " . $user_email['email'] . "\n";

            if ($user_email) {

                $user_username = $pdo->prepare("SELECT username FROM USER WHERE id = :id_user");
                $user_username->bindParam(':id_user', $user['id_user'], PDO::PARAM_INT);
                $user_username->execute();
                $username = $user_username->fetch(PDO::FETCH_ASSOC);

                $mail_content = "
                Votre équipe bénévole, <strong>$team_name</strong>, a un évènement aujourd'hui : <strong>{$activity['name']}</strong> auquel vous êtes inscrits.<br>
                <br>Pour accèder aux détails de l'évènement, veuillez vous rendre sur le lien suivant : <br><a href='https://petisign.cloud/Sources/Benevoles/view_activity.php?id=". $activity['id'] . "' >https://petisign.cloud/Sources/Benevoles/view_activity.php?id=". $activity['id'] ."</a><br>
                ";

                $check_if_city_is_set = $pdo->prepare("SELECT city, postal_code, rue, num FROM TEAM_ACTIVITY WHERE id = :id");
                $check_if_city_is_set->bindParam(':id', $activity['id'], PDO::PARAM_INT);
                $check_if_city_is_set->execute();
                $city = $check_if_city_is_set->fetch(PDO::FETCH_ASSOC);

                if ($city['city']) {
                    $mail_content .= "<br>Voici le point de rendez-vous donné par l'organisateur :<br><address>". $city['num'] . " " . $city['rue'] . ",<br>" . $city['postal_code'] . ",<br>" . $city['city'] . "</address>";
                }

                $mail_content .= "<br>Merci de votre participation et à bientôt !<br>
                L'équipe de Petisign";

                $mail_sent = new PHPMailer(true);
                EnvoieMail($mail_sent, $user_email['email'], $username['username'], "Évènement aujourd'hui !", $mail_content);

            }
        }

    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>