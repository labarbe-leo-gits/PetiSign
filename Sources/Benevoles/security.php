<?php

session_start();

if(!isset($_SESSION['mail'])) {
    header('Location: /Sources/login.php');
    exit();
}

if($is_benevole != 1 && $is_admin != 1) {
    header('Location: /Sources/error.php?code=403');
    exit();
}