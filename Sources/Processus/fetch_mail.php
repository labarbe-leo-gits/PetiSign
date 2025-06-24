<?php
header('Content-Type: application/json');
include_once '../database/database.php';

$type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$value = filter_input(INPUT_GET, 'value', FILTER_SANITIZE_STRING);

if (!$type || !$value) {
    echo json_encode(['status' => 'invalid']);
    exit();
}

try {
    if ($type === 'email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'invalid']);
            exit();
        }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE email = :value");
    } elseif ($type === 'username') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE username = :value");
    } else {
        echo json_encode(['status' => 'invalid']);
        exit();
    }
    
    $stmt->bindParam(':value', $value);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count > 0) {
        echo json_encode(['status' => 'taken']);
    } else {
        echo json_encode(['status' => 'available']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error']);
}
?>