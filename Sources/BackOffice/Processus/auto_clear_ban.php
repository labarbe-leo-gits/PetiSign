<?php

include_once '../../database/database.php';

$envfile = "/var/www/html/Sources/BackOffice/Processus/key.env";
$env = parse_ini_file($envfile);
$key = $env["CrontabKey"];

$logFile = "/var/www/html/Sources/logs/log.txt";

if($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['key']) || $_GET['key'] !== $key) {
    header('Location: /Sources/error.php?code=403');
    exit();
}

$get_all_bans = $pdo->prepare("SELECT id, id_user, expiration FROM BAN");
$get_all_bans->execute();
$all_bans = $get_all_bans->fetchAll(PDO::FETCH_ASSOC);

foreach($all_bans as $ban) {

    $ban_id = $ban['id'];
    $ban_user_id = $ban['id_user'];
    $ban_expiration = $ban['expiration'];
    $current_date = date('Y-m-d');
    $ban_expiration_date = date('Y-m-d', strtotime($ban_expiration));

    $get_username_from_id = $pdo->prepare("SELECT username FROM USER WHERE id = :id");
    $get_username_from_id->bindParam(':id', $ban_user_id);
    $get_username_from_id->execute();
    $username = $get_username_from_id->fetchColumn();

    if($ban_expiration_date < $current_date) {
        $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id = :id");
        $delete_ban->bindParam(':id', $ban_id);
        $delete_ban->execute();
        file_put_contents($logFile, date('d/m/Y H:i') . " UTC - [SYSTEM] - CronTab - 0.0.0.0 - Déban de ". $username ."\n");
    }

}
?>