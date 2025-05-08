<?php
$login = json_decode(file_get_contents('LOGIN.json'), true);
$current = null;
foreach ($login as $user) {
    if ($user['actif'] === true) {
        $current = $user;
        $isAdmin = ($user['role'] === 'Admin');
        $isTrad = ($user['role'] === 'Traducteur' && $user['status'] === 'validé');
        break;
    }
}
if (!($isAdmin || $isTrad)) {
    header('Location: index.php');
    exit;
}
$recipes = json_decode(file_get_contents('recipes.json'), true);

$modified = false;
foreach ($recipes as &$r) {
    if (!isset($r['id'])) {
        $r['id'] = md5($r['name'] . $r['Author']);
        $modified = true;
    }
}


if ($modified) {
    file_put_contents('recipes.json', json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
?>
<!DOCTYPE html>
<html>

<head>
    <a href="site.php">retour</a>
    <title>Traduction des Recettes</title>
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <h2>Traduction des recettes</h2>
    <div class="recipe-grid">
        <?php foreach ($recipes as $r):
            $id = $r['id'];
            $title = $r['name'] ?? 'Sans titre';
            $img = $r['imageURL'] ?? 'placeholder.jpg';
        ?>
            <div class="recipe-card">
                <img src="<?= htmlspecialchars($img) ?>" class="recipe-img" alt="Image recette">
                <div class="recipe-title"><?= htmlspecialchars($title) ?></div>
                <a class="translate-button" href="traduire_recette.php?id=<?= $id ?>">Traduire</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>