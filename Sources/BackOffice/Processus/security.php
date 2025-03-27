<?php
session_start();
include_once '../../database/database.php';

$is_admin_stmt = $pdo->prepare("SELECT is_admin FROM USER WHERE email = :mail");
$is_admin_stmt->bindParam(':mail', $_SESSION['mail']);
$is_admin_stmt->execute();
$is_admin = $is_admin_stmt->fetchColumn();

?>