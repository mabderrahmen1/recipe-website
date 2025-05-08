<?php
$jsonString = file_get_contents("LOGIN.json");

$data = json_decode($jsonString, true);

$emailExiste = false;

foreach ($data as $utilisateur) {
    if ($utilisateur['email'] === $_GET['email']) {
        $emailExiste = true;
        break;
    }
}

echo $emailExiste ? "true" : "false";
?>