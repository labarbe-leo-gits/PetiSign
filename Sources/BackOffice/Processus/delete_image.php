<?php
include_once '../../loading.php';
include_once '../../../database/database.php';
include_once 'security.php';

if($id_admin != 0){

    $file = filter_input(INPUT_GET, 'image', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $file_name_without_extension = pathinfo($file, PATHINFO_FILENAME);
    $num = ($file_name_without_extension == 1) ? rand(2, 10) : rand(1, $file_name_without_extension - 1);
    $exists = file_exists("../../../Resources/img/petition_selection/" . $num . ".jpg");

    $count_number_of_images_inside_the_directory = glob("../../../Resources/img/petition_selection/*.jpg");
    $count_number_of_images_inside_the_directory = count($count_number_of_images_inside_the_directory);
    
    if ($count_number_of_images_inside_the_directory <= 1) {
        header('Location: /Sources/BackOffice/database_gestion.php?code=DeleteFailed');
        exit();
    }

    while(!$exists){
        $num = ($file_name_without_extension == 1) ? rand(2, 10) : rand(1, $file_name_without_extension - 1);
        $exists = file_exists("../../../Resources/img/petition_selection/" . $num . ".jpg");
    }

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

            $get_petitions_that_have_this_img = $pdo->prepare("SELECT id FROM PETITION WHERE image_id = :id");
            $get_petitions_that_have_this_img->bindParam(':id', $file_name_without_extension, PDO::PARAM_INT);
            $get_petitions_that_have_this_img->execute();
            $petitions = $get_petitions_that_have_this_img->fetchAll(PDO::FETCH_ASSOC);

            if(count($petitions) > 0){
                foreach ($petitions as $petition) {
                    $delete_petition = $pdo->prepare("UPDATE PETITION SET image_id = :img_id WHERE id = :id");
                    $delete_petition->bindParam(':img_id', $num, PDO::PARAM_INT);
                    $delete_petition->bindParam(':id', $petition['id'], PDO::PARAM_INT);
                    $delete_petition->execute();
                }
            }

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