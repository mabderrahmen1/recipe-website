<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $data = json_decode(file_get_contents('LOGIN.json'), true);

    foreach ($data as &$user) {
        if (isset($user['actif']) && $user['actif'] == true) {
            $user['lang'] = $lang;
            break;
        }
    }

    file_put_contents('LOGIN.json', json_encode($data, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
    exit;
}
echo json_encode(['success' => false]);
?>
