<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$env = parse_ini_file('glpi.env');
$app_token = $env["APP_TOKEN"];
$user_token = $env["USER_TOKEN"];

$glpi_url = 'https://petisign.cloud/glpi/apirest.php';
$app_token = $app_token;
$user_token = $user_token;
$default_requester_id = 2; 
$last_api_error = '';
$last_api_status = 0;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Méthode non autorisée. Utilisez POST.');
    exit;
}

error_log('Début de traitement de la requête');

$json_data = file_get_contents('php://input');
error_log('Données reçues: ' . $json_data);

$data = json_decode($json_data, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log('Erreur de décodage JSON: ' . json_last_error_msg());
    sendResponse(false, 'Données JSON invalides: ' . json_last_error_msg());
    exit;
}
if (!isset($data['title']) || !isset($data['description']) || !isset($data['email']) || !isset($data['name'])) {
    sendResponse(false, 'Données manquantes');
    exit;
}

try {

    $session_token = initSession();
    if (!$session_token) {
        sendResponse(false, 'Impossible de se connecter à GLPI');
        exit;
    }
    
    error_log('Session initialisée avec succès: ' . $session_token);

    $requester_id = $default_requester_id;
    error_log('Utilisation de l\'ID de requester par défaut: ' . $requester_id);

    $ticket_id = createTicket($data, $requester_id, $session_token);
    if (!$ticket_id) {
        killSession($session_token);
        sendResponse(false, 'Échec de la création du ticket: ' . getLastAPIError());
        exit;
    }
    
    error_log('Ticket créé avec ID: ' . $ticket_id);
    
    killSession($session_token);
    
    sendResponse(true, 'Ticket créé avec succès', $ticket_id);

} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    if (isset($session_token) && $session_token) {
        killSession($session_token);
    }
    sendResponse(false, 'Erreur: ' . $e->getMessage());
}

function initSession() {
    global $glpi_url, $app_token, $user_token;
    
    $init_url = $glpi_url . '/initSession';
    $headers = [
        'Content-Type: application/json',
        'App-Token: ' . $app_token
    ];

    $post_data = json_encode(['user_token' => $user_token]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $init_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!empty($curl_error)) {
        error_log("Erreur cURL lors de l'initialisation de la session: " . $curl_error);
        setLastAPIError($status_code, $response, $curl_error);
        return false;
    }
    
    if ($status_code == 200) {
        $result = json_decode($response, true);
        if (isset($result['session_token'])) {
            return $result['session_token'];
        } else {
            error_log("Token de session non trouvé dans la réponse: " . $response);
            setLastAPIError($status_code, $response, "Token de session non trouvé");
        }
    } else {
        error_log("Échec de l'initialisation de la session. Code: $status_code, Réponse: $response");
        setLastAPIError($status_code, $response);
    }
    
    return false;
}

function createTicket($data, $requester_id, $session_token) {
    global $glpi_url, $app_token;
    
    $ticket_url = $glpi_url . '/Ticket';
    $headers = [
        'Content-Type: application/json',
        'App-Token: ' . $app_token,
        'Session-Token: ' . $session_token
    ];

    $title_len = mb_strlen($data['title']);
    if ($title_len > 60) {
        error_log("Titre trop long: $title_len caractères (max 60)");
        setLastAPIError(400, '', "Le titre du ticket ne doit pas dépasser 60 caractères.");
        return false;
    }

    $description_len = mb_strlen($data['description']);
    if ($description_len > 600) {
        error_log("Description trop longue: $description_len caractères (max 600)");
        setLastAPIError(400, '', "La description du ticket ne doit pas dépasser 600 caractères.");
        return false;
    }
    
    $formatted_content = "Demandeur : " . $data['name'] . " " . $data['firstname'] . "\n";
    $formatted_content .= "Mail : " . $data['email'] . "\n\n";
    $formatted_content .= "Contenu du message : \n" . $data['description'];
    
    $ticket_data = [
        'input' => [
            'name' => $data['title'],
            'content' => $formatted_content,
            '_users_id_requester' => $requester_id,
            'urgency' => isset($data['urgency']) ? $data['urgency'] : 3,
            '_users_id_recipient' => $requester_id,
            '_users_mail_notification' => ['use_notification' => 1, 'alternative_email' => $data['email']]
        ]
    ];

    if (!empty($data['category'])) {
        $ticket_data['input']['itilcategories_id'] = $data['category'];
    }
    
    error_log("Tentative de création de ticket avec les données: " . json_encode($ticket_data));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ticket_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($ticket_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $curl_error = curl_error($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if (!empty($curl_error)) {
        error_log("Erreur cURL lors de la création du ticket: " . $curl_error);
        setLastAPIError($status_code, $response, $curl_error);
        return false;
    }
    
    error_log("Réponse de création de ticket: Code: $status_code, Réponse: $response");
    
    if ($status_code == 201) {
        $result = json_decode($response, true);
        if (isset($result['id'])) {
            return $result['id'];
        }
    }
    
    setLastAPIError($status_code, $response);
    return false;
}

function killSession($session_token) {
    global $glpi_url, $app_token;
    
    $logout_url = $glpi_url . '/killSession';
    $headers = [
        'Content-Type: application/json',
        'App-Token: ' . $app_token,
        'Session-Token: ' . $session_token
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $logout_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    curl_exec($ch);
    curl_close($ch);
    
    error_log("Session terminée: " . $session_token);
}

function setLastAPIError($status, $response, $error = '') {
    global $last_api_error, $last_api_status;
    
    $last_api_status = $status;
    
    if (!empty($error)) {
        $last_api_error = $error;
    } else if ($status >= 400) {
        $resp_obj = json_decode($response, true);
        if (isset($resp_obj['message'])) {
            $last_api_error = $resp_obj['message'];
        } else {
            $last_api_error = "Erreur de l'API (Status $status)";
        }
    } else {
        $last_api_error = "Erreur inconnue de l'API";
    }
    
    error_log("API Error set: $last_api_error (Status: $status)");
}

function getLastAPIError() {
    global $last_api_error, $last_api_status;
    
    if (!empty($last_api_error)) {
        return $last_api_error;
    }
    
    if ($last_api_status >= 400) {
        return "Erreur HTTP " . $last_api_status;
    }
    
    return "Erreur inconnue";
}

function sendResponse($success, $message, $ticket_id = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($ticket_id) {
        $response['ticket_id'] = $ticket_id;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>