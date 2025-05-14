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

    $get_last_login_from_users = $pdo->prepare("SELECT last_login, username, email FROM USER");
    $get_last_login_from_users->execute();
    $last_logins = $get_last_login_from_users->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($last_logins) . " users to check.\n";
    
    foreach($last_logins as $user) {
        $last_login = $user['last_login'];
        $current_date = new DateTime();
        $last_login_date = new DateTime($last_login);
        
        $interval = $current_date->diff($last_login_date);
        $days_since_last_login = $interval->days;
        
        if ($days_since_last_login > 30) {
            $mail_sent = new PHPMailer(true);
            echo "User ". $user['username'] ." has not logged in for more than 30 days.\n";
            EnvoieMail($mail_sent, $user['email'], $user['username'], "Rappel de connexion", "Nous avons remarqué que vous n'avez pas été actif sur notre plateforme depuis ". $days_since_last_login ." jours.<br />Nous aimerions vous rappeler de vous connecter pour profiter de nos services.<br />Si vous le souhaitez, vous pouvez également supprimer votre compte via votre page de profil PétiSign?<br /><br />Cordialement,<br />L'équipe de support.");
            echo "Email sent to ". $user['username'] ."\n";
        } else {
            echo "User ". $user['username'] ." has logged in within the last 30 days.\n";
        }
    }
    
    
    foreach($all_bans as $ban) {
        $ban_id = $ban['id'];
        $ban_user_id = $ban['id_user'];
        $ban_expiration = $ban['expiration'];
        
        $current_date = new DateTime();
        $ban_expiration_date = new DateTime($ban_expiration);
        
        $get_username_from_id = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
        $get_username_from_id->bindParam(':id', $ban_user_id);
        $get_username_from_id->execute();
        $username = $get_username_from_id->fetchColumn();
        
        echo "Checking ban for user: $username (expires: {$ban_expiration})\n";
        
        if($ban_expiration_date < $current_date) {
            echo "Ban expired for $username. Removing...\n";
            
            try {
                $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id = :id");
                $delete_ban->bindParam(':id', $ban_id);
                $delete_ban->execute();
                echo "Le ban de l'utilisateur " . $username . " a été supprimé avec succès.\n";
                
                if (file_put_contents($logFile, date('d/m/Y H:i') . " UTC - [SYSTEM] - CronTab - 0.0.0.0 - Rappel de connexion de ". $username ."\n", FILE_APPEND) === false) {
                    echo "Warning: Failed to write to log file.\n";
                }
            } catch (PDOException $e) {
                echo "Error deleting ban: " . $e->getMessage() . "\n";
                file_put_contents($logFile, date('d/m/Y H:i') . $e->getMessage());
            }
        } else {
            echo "Ban still active for $username.\n";
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>