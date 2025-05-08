<?php
$jsonFile = 'LOGIN.json';
$data = json_decode(file_get_contents($jsonFile), true);

foreach ($data as $key => $user) {
    if ($user['actif'] === true) {
        $data[$key]['actif'] = false;
        break; 
    }
}

file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

header('Location: index.php');
exit();
?>