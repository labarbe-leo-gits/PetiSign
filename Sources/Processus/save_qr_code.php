<?php
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['filename']) || !isset($input['imageData'])) {
        throw new Exception('Missing filename or image data');
    }
    
    $filename = $input['filename'];
    $imageData = $input['imageData'];
    
    $imageData = preg_replace('/^data:image\/png;base64,/', '', $imageData);
    
    $decodedData = base64_decode($imageData);
    
    if ($decodedData === false) {
        throw new Exception('Failed to decode base64 data');
    }
    
    $directory = '../../Resources/qrcode/';
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
    
    $filepath = $directory . $filename;
    $result = file_put_contents($filepath, $decodedData);
    
    if ($result === false) {
        throw new Exception('Failed to save file');
    }
    
    echo json_encode(['success' => true, 'message' => 'QR code saved successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>