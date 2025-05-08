<?php
$login = json_decode(file_get_contents('LOGIN.json'), true);
$current = null;
foreach ($login as $user) {
    if ($user['actif'] === true) {
        $current = $user;
        $isAdmin = ($user['role'] === 'Admin');
        $isTrad = ($user['role'] === 'Traducteur' && $user['status'] === 'validé');
        $isChef = ($user['role'] === 'Chef' && $user['status'] === 'validé');
        $userEmail = $user['email'];
        break;
    }
}

if (!($isAdmin || $isTrad)) {
    header('Location: index.php');
    exit;
}

$recipes = json_decode(file_get_contents('recipes.json'), true);
$id = $_GET['id'] ?? null;
$targetRecipe = null;

foreach ($recipes as &$r) {
    if ($r['id'] == $id) {
        $targetRecipe = &$r;
        break;
    }
}

if (!$targetRecipe) {
    echo "Recette non trouvée.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
   
    if ($isAdmin || ($isTrad && empty($targetRecipe['nameFR']) && !empty($targetRecipe['name']))) {
        $input = trim($_POST['fr']['name']);
        $targetRecipe['nameFR'] = $input;
    }

    if ($isAdmin || ($isTrad && count(array_filter($targetRecipe['ingredientsFR'] ?? [], fn($i) => !empty($i['name']))) < count($targetRecipe['ingredients']))) {
        $lines = array_map('trim', explode("\n", $_POST['fr']['ingredients']));
        $targetRecipe['ingredientsFR'] = [];
        foreach ($lines as $index => $line) {
            $targetRecipe['ingredientsFR'][] = [
                'quantity' => $targetRecipe['ingredients'][$index]['quantity'] ?? '',
                'name' => $line,
                'type' => $targetRecipe['ingredients'][$index]['type'] ?? ''
            ];
        }
    }

    if ($isAdmin || ($isTrad && count($targetRecipe['stepsFR'] ?? []) < count($targetRecipe['steps']))) {
        $targetRecipe['stepsFR'] = array_map('trim', explode("\n", $_POST['fr']['steps']));
    }

    file_put_contents('recipes.json', json_encode($recipes, JSON_PRETTY_PRINT));
    echo "<p style='color:green;'>Traduction enregistrée.</p>";
}
?>
<html>
<head>
    <a href="traduction.php">retour</a>
    <title>Traduire recette</title>
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .recipe-preview {
            width: 45%;
            border-right: 1px solid #ccc;
            padding-right: 20px;
        }
        .translation-form {
            width: 50%;
            padding-left: 20px;
        }
        img {
            max-width: 100%;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="recipe-preview">
        <h2>Recette originale : <?= htmlspecialchars($targetRecipe['name'] ?? '') ?></h2>
        <?php if (!empty($targetRecipe['image'])): ?>
            <img src="<?= htmlspecialchars($targetRecipe['image']) ?>" alt="image recette">
        <?php endif; ?>

        <h3>Ingrédients (EN)</h3>
        <ul>
            <?php foreach ($targetRecipe['ingredients'] as $ing): ?>
                <li><?= htmlspecialchars($ing['name']) ?></li>
            <?php endforeach; ?>
        </ul>

        <h3>Étapes (EN)</h3>
        <ol>
            <?php foreach ($targetRecipe['steps'] as $step): ?>
                <li><?= htmlspecialchars($step) ?></li>
            <?php endforeach; ?>
        </ol>
    </div>

    <div class="translation-form">
        <h2>Traduction de la recette: <?= htmlspecialchars($targetRecipe['nameFR'] ?? '') ?></h2>
        <form method="POST">
            <h3>Nom de la recette</h3>
            <div style="display:flex; gap:10px;">
                <?php foreach (['fr', 'en'] as $lang): ?>
                    <div>
                        <label><?= strtoupper($lang) ?>:</label><br>
                        <?php
                        $value = ($lang === 'fr') ? ($targetRecipe['nameFR'] ?? '') : ($targetRecipe['name'] ?? '');
                        $readonly = '';
                        if ($lang === 'fr' && $isTrad && !$isAdmin && !($isChef && $targetRecipe['auteur'] === $userEmail)) {
                            if (!empty($value) || empty($targetRecipe['name'])) {
                                $readonly = 'readonly';
                            }
                        }
                        ?>
                        <input name="<?= $lang ?>[name]" value="<?= htmlspecialchars($value) ?>" <?= $readonly ?> style="width:300px;">
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>Ingrédients</h3>
            <div style="display:flex; gap:10px;">
                <?php foreach (['fr', 'en'] as $lang): ?>
                    <div>
                        <label><?= strtoupper($lang) ?>:</label><br>
                        <textarea name="<?= $lang ?>[ingredients]" rows="5" cols="30"><?= htmlspecialchars(
                            implode("\n", $lang === 'fr'
                                ? array_column($targetRecipe['ingredientsFR'] ?? [], 'name')
                                : array_column($targetRecipe['ingredients'] ?? [], 'name')
                            )
                        ) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3>Étapes</h3>
            <div style="display:flex; gap:10px;">
                <?php foreach (['fr', 'en'] as $lang): ?>
                    <div>
                        <label><?= strtoupper($lang) ?>:</label><br>
                        <textarea name="<?= $lang ?>[steps]" rows="5" cols="30"><?= htmlspecialchars(
                            implode("\n", $targetRecipe[$lang === 'fr' ? 'stepsFR' : 'steps'] ?? [])
                        ) ?></textarea>
                    </div>
                <?php endforeach; ?>
            </div>

            <br><input type="submit" name="save" value="Enregistrer">
        </form>
    </div>
</div>
</body>
</html>
