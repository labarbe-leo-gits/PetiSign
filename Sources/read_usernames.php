<?php

// read json/banned_usernames_EN.json
$filename = 'json/banned_username.json';
if (file_exists($filename)) {
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
} else {
    echo "File not found.";
    exit;
}

$username = "adolfdu33";

// print the contents of the json file
if (isset($data['banned_usernames'])) {
    $banned_usernames = $data['banned_usernames'];
    print_r($banned_usernames);
    // check if the username is in the banned usernames
    if (in_array($username, $banned_usernames)) {
        echo "The username $username is banned.";
        exit;
    } else {
        echo "The username $username is not banned.";
        exit;
    }
    
} else {
    echo "No banned usernames found.";
    exit;
}

?>