<?php

$json_EN = file_get_contents('json/banned_username_EN.json');
$json_FR = file_get_contents('json/banned_username_FR.json');
$banned_usernames_EN = json_decode($json_EN, true);
$banned_usernames_FR = json_decode($json_FR, true);

$banned_usernames = array_merge($banned_usernames_EN, $banned_usernames_FR);
$banned_usernames = array_map('strtolower', $banned_usernames);

print_r($banned_usernames);

?>