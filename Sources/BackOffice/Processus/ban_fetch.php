<?php

include_once __DIR__ '/../../database/database.php';
include_once '/../../Processus/write_logs.php';

$get_all_bans = $pdo->prepare("SELECT * FROM BAN");
$get_all_bans->execute();
$bans = $get_all_bans->fetchAll(PDO::FETCH_ASSOC);

$ban_num_stmt = $pdo->prepare("SELECT COUNT(*) FROM BAN");
$ban_num_stmt->execute();
$ban_num = $ban_num_stmt->fetchColumn();

if($ban_num === 0){
    exit;
}

foreach ($bans as $ban) {
    if ($ban['expiration'] <= date('Y-m-d')) {
        $delete_ban = $pdo->prepare("DELETE FROM BAN WHERE id = :id");
        $delete_ban->bindParam(':id', $ban['id']);
        $delete_ban->execute();

        write_logs("/Sources/logs/log.txt", "[ACTION]", "Unbanned user");

    }
}


?>