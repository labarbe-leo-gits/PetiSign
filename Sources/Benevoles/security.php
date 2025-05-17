<?php
if($is_benevole != 1) {
    header('Location: /Sources/error.php?code=403');
    exit();
}