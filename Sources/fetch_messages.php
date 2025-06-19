<?php
session_start();
include_once 'database/database.php';

header('Content-Type: application/json');

$id_discussion = filter_input(INPUT_GET, 'discussion_id', FILTER_SANITIZE_NUMBER_INT);
if (!$id_discussion) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid discussion ID']);
    exit();
}

$lastMessageId = filter_input(INPUT_GET, 'last_id', FILTER_SANITIZE_NUMBER_INT) ?: 0;

try {

    $check_new_messages = $pdo->prepare("SELECT MAX(id) as max_id FROM MESSAGE WHERE id_discussion = :id_discussion");
    $check_new_messages->bindParam(':id_discussion', $id_discussion, PDO::PARAM_INT);
    $check_new_messages->execute();
    $current_max_id = $check_new_messages->fetchColumn() ?: 0;
    
    if ($current_max_id <= $lastMessageId && $lastMessageId > 0) {
        echo json_encode(['status' => 'no_change', 'last_id' => $current_max_id]);
        exit();
    }

    $get_msg_stmt = $pdo->prepare("SELECT * FROM MESSAGE WHERE id_discussion = :id_discussion");
    $get_msg_stmt->bindParam(':id_discussion', $id_discussion, PDO::PARAM_INT);
    $get_msg_stmt->execute();
    $messages = $get_msg_stmt->fetchAll(PDO::FETCH_ASSOC);

    $output = '';
    if (count($messages) > 0) {
        foreach ($messages as $message) {
            $get_user_stmt = $pdo->prepare("SELECT username, email FROM USER WHERE id = :sender_id");
            $get_user_stmt->bindParam(':sender_id', $message['sender'], PDO::PARAM_INT);
            $get_user_stmt->execute();
            $user = $get_user_stmt->fetch(PDO::FETCH_ASSOC);
            $username = $user['username'];
            $email = $user['email'];
            $class = ($email == $_SESSION['mail']) ? 'self_msg' : 'other_msg';
            
            $output .= '<li class="message-item">';
            $output .= '<p class="' . $class . '">' . htmlspecialchars($message['content']) . '</p>';
            if ($email == $_SESSION['mail']) {
                $output .= '<button class="delete-message" data-message-id="' . $message['id'] . '" onclick="window.location.href=\'Processus/delete_message.php?id='. $message['id'] .'\'"><img src="/Resources/img/ui_icons/trash.png" alt="Supprimer"></button>';
            }
            $output .= '</li>';
        }
    } else {
        $output = '<li class="warning">Aucun message pour le moment. Envoyez en un !</li>';
    }
    
    echo json_encode([
        'status' => 'update',
        'html' => $output,
        'last_id' => $current_max_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>