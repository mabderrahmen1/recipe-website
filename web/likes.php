<?php
header('Content-Type: application/json');

$likesFile = 'likes.json';
$likesData = file_exists($likesFile) ? json_decode(file_get_contents($likesFile), true) : [];

function enregistrerLike($postId, $action, $userId)
{
    global $likesData, $likesFile;

    if (!is_array($likesData)) {
        $likesData = [];
    }

    if (!isset($likesData[$postId])) {
        $likesData[$postId] = ['like' => 0, 'dislike' => 0, 'users' => []];
    }

    $userState = $likesData[$postId]['users'][$userId] ?? null;

    switch ($action) {
        case 'like':
            if ($userState === 'liked') {
                break;
            }
            if ($userState === 'disliked') {
                $likesData[$postId]['dislike']--;
            }
            $likesData[$postId]['like']++;
            $likesData[$postId]['users'][$userId] = 'liked';
            break;

        case 'dislike':
            if ($userState === 'disliked') {
                break;
            }
            if ($userState === 'liked') {
                $likesData[$postId]['like']--;
            }
            $likesData[$postId]['dislike']++;
            $likesData[$postId]['users'][$userId] = 'disliked';
            break;

        case 'unlike':
            if ($userState === 'liked') {
                $likesData[$postId]['like']--;
                unset($likesData[$postId]['users'][$userId]);
            }
            break;

        case 'undislike':
            if ($userState === 'disliked') {
                $likesData[$postId]['dislike']--;
                unset($likesData[$postId]['users'][$userId]);
            }
            break;
    }


    file_put_contents($likesFile, json_encode($likesData, JSON_PRETTY_PRINT));

    return $likesData[$postId];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['postId'] ?? null;
    $action = $_POST['action'] ?? null;
    $userId = $_POST['userId'] ?? 'anonymous';

    if ($postId !== null && in_array($action, ['like', 'dislike', 'unlike', 'undislike'])) {
        $resultat = enregistrerLike($postId, $action, $userId);
        echo json_encode(['like' => $resultat['like'], 'dislike' => $resultat['dislike']]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Données invalides']);
    }
}
