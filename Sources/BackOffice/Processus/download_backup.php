<?php
include_once '../../../database/database.php';
include_once 'security.php';

if($id_admin != 0){

    $file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $path_to_file = "../../logs/archives/";
    $full_path = $path_to_file . $file;

    if (file_exists($full_path) && is_readable($full_path)) {
        $filesize = filesize($full_path);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($full_path).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $filesize);

        ob_clean();
        flush();
        
        readfile($full_path);
        exit;
    } else {

        echo "Error: File not found or not readable.";
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>