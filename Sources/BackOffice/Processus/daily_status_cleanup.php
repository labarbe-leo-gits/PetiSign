<?php

$database_path = "/var/www/html/Sources/database/database.php";

include_once($database_path);

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

    $get_all_users = $pdo->prepare("SELECT id, user_daily_status FROM USER");
    $get_all_users->execute();
    $all_users = $get_all_users->fetchAll(PDO::FETCH_ASSOC);
    
    foreach($all_users as $user) {

        $user_id = $user['id'];
        echo "<br>";
        echo "Processing user ID: $user_id\n";
        $daily_user_status = $user['user_daily_status'];
        echo "<br>";
        echo "Current daily status for user ID $user_id: $daily_user_status\n";
        echo "<br>";
        echo "Is null ? " . (is_null($daily_user_status) ? 'Yes' : 'No') . "\n";
        echo "<br>";

        if ($daily_user_status == null) {
            continue;
        }
        
        $update_stmt_to_null = $pdo->prepare("UPDATE USER SET user_daily_status = NULL WHERE id = :id");
        $update_stmt_to_null->bindParam(':id', $user_id);
        $update_stmt_to_null->execute();
        
    }

    echo "User daily status has been successfully set to NULL for all users.\n";

    file_put_contents($logFile, date('d/m/Y H:i') . " UTC - [SYSTEM] - CronTab - 0.0.0.0 - Suppression des status utilisateur\n", FILE_APPEND);

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>
