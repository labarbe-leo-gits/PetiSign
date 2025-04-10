<?php
session_start();
include_once '../../database/database.php';

if (isset($_SESSION['mail'])) {
    $is_benevole_stmt = $pdo->prepare("SELECT is_benevole FROM USER WHERE email = :mail");
    $is_benevole_stmt->bindParam(':mail', $_SESSION['mail']);
    $is_benevole_stmt->execute();
    $is_benevole = $is_benevole_stmt->fetchColumn();

    $id_benevole = ($is_benevole == 1) ? 1 : 0;
} else {
    $id_benevole = 0;
}
?>