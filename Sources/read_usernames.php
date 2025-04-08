<?php

// read json/banned_usernames_EN.json
$filename = 'json/banned_username_EN.json';
if (file_exists($filename)) {
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
} else {
    echo "File not found.";
    exit;
}

// print the contents of the json file
if (isset($data['banned_usernames'])) {
    $banned_usernames = $data['banned_usernames'];
    print_r($banned_usernames);
} else {
    echo "No banned usernames found.";
    exit;
}

?>