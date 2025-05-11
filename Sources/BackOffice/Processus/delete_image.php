<?php
include_once '../../loading.php';
include_once '../../../database/database.php';
include_once 'security.php';

if($id_admin != 0){

    $file = filter_input(INPUT_GET, 'image', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $path_to_file = "../../../Resources/img/petition_selection/";
    $full_path = $path_to_file . $file;
    
    $real_path = realpath($full_path);
    $archives_dir = realpath($path_to_file);

    if ($real_path && is_file($real_path) && strpos($real_path, $archives_dir) === 0) {

        $deleted = false;

        @chmod($real_path, 0777);
        if (@unlink($real_path)) {
            $deleted = true;
        }

        if (!$deleted) {
            clearstatcache();
            if (@unlink($real_path)) {
                $deleted = true;
            } 
        }

        if (!$deleted && function_exists('shell_exec')) {
            $escapedPath = escapeshellarg($real_path);

            shell_exec("chmod 777 $escapedPath 2>&1");

            $result = shell_exec("rm -f $escapedPath 2>&1");
            clearstatcache();
            if (!file_exists($real_path)) {
                $deleted = true;
            }
        }
        
        
        if ($deleted || !file_exists($real_path)) {
            header('Location: /Sources/BackOffice/database_gestion.php?code=DeleteSuccess');
            exit();
        } else {

            header('Location: /Sources/BackOffice/database_gestion.php?code=DeleteFailed');
            exit();
        }
    } else {

        header('Location: /Sources/BackOffice/database_gestion.php?code=FileNotFound');
        exit();
    }
} else {
    header('Location: /Sources/error.php?code=403');
    exit();
}
?>