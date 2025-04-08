<?php

$dir = '/Resources/img/petition_selection';
$full_path = $_SERVER['DOCUMENT_ROOT'] . $dir;

$files = scandir($full_path);
$files = array_diff($files, array('.', '..'));

$file_numbers = [];
foreach ($files as $file) {
    $filename = pathinfo($file, PATHINFO_FILENAME);
    if (is_numeric($filename)) {
        $file_numbers[] = (int)$filename;
    }
}

sort($file_numbers);
$next_logical_file_name = 1;

foreach ($file_numbers as $number) {
    if ($number == $next_logical_file_name) {
        $next_logical_file_name++;
    } else {
        break;
    }
}

$target_dir = "img/petition_selection/";
$original_file_name = basename($_FILES["fileToUpload"]["name"]);
$file_extension = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
$target_file = $target_dir . $next_logical_file_name . "." . $file_extension;
$uploadOk = 1;

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (file_exists($target_file)) {
    echo "Sorry, a file with the name $file_name.$file_extension already exists.<br>";
    $uploadOk = 0;
}

$allowed_types = array("jpg");
if (!in_array($file_extension, $allowed_types)) {
    echo "Sorry, this file type is not allowed. Permitted types: " . implode(", ", $allowed_types) . "<br>";
    $uploadOk = 0;
}

// Attempt to upload the file
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.<br>";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file has been uploaded successfully.<br>";
        echo "Stored as: " . htmlspecialchars($file_name . "." . $file_extension);
    } else {
        echo "Sorry, there was an error uploading your file.<br>";
        echo "Upload error code: " . $_FILES["fileToUpload"]["error"];
    }
}
?>