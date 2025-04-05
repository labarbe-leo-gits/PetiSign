<?php
$target_dir = "img/petition_selection/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (file_exists($target_file)) {
    echo "Sorry, the file already exists.<br>";
    $uploadOk = 0;
}

if ($_FILES["fileToUpload"]["size"] > 5000000) {
    echo "Sorry, your file is too large.<br>";
    $uploadOk = 0;
}

$allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx", "txt", "csv", "xls", "xlsx", "zip", "rar", "mp4", "mp3");
if (!in_array($fileType, $allowed_types)) {
    echo "Sorry, this file type is not allowed. Permitted types: " . implode(", ", $allowed_types) . "<br>";
    $uploadOk = 0;
}

if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.<br>";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded successfully.<br>";
        echo "Stored as: " . basename($target_file);
    } else {
        echo "Sorry, there was an error uploading your file.<br>";
        echo "Upload error code: " . $_FILES["fileToUpload"]["error"];
    }
}
?>