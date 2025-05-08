<?php

function saveRecipes($recipes)
{
    file_put_contents('recipes.json', json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['name']) || empty($_POST['ingredients']) || empty($_POST['steps'])) {
        $error = "Il faut remplir tous les champs obligatoires !";
    } else {

        $newRecipe = [
            'name' => $_POST['name'],
            'nameFR' => $_POST['nameFR'] ?? '',
            'Author' => $_POST['author'] ?? 'Unknown',
            'Without' => isset($_POST['without']) ? explode(',', $_POST['without']) : [],
            'ingredients' => array_map('trim', explode("\n", $_POST['ingredients'])),
            'ingredientsFR' => isset($_POST['ingredientsFR']) ? array_map('trim', explode("\n", $_POST['ingredientsFR'])) : [],
            'steps' => array_map('trim', explode("\n", $_POST['steps'])),
            'stepsFR' => isset($_POST['stepsFR']) ? array_map('trim', explode("\n", $_POST['stepsFR'])) : [],
            'timers' => isset($_POST['timers']) ? array_map('intval', explode(',', $_POST['timers'])) : [],
            'imageURL' => $_POST['imageURL'] ?? '',
            'originalURL' => $_POST['originalURL'] ?? ''
        ];

        $recipes = [];
        if (file_exists('recipes.json')) {
            $recipes = json_decode(file_get_contents('recipes.json'), true) ?? [];
        }

        $recipes[] = $newRecipe;

        saveRecipes($recipes);

        header('Location: ajouterecette.php?success=1');
        exit();
    }
}

$recipeCount = 0;
if (file_exists('recipes.json')) {
    $recipes = json_decode(file_get_contents('recipes.json'), true);
    $recipeCount = is_array($recipes) ? count($recipes) : 0;
}
?>

<!DOCTYPE html>

<head>

    <title>Ajouter une Recette</title>
    <link rel="stylesheet" href="ajouteRecette.css">


</head>

<body>
    <h1>Ajouter une Nouvelle Recette</h1>
    <div class="Retour">
        <a href="site.php" type="button " class="retourButton">Retour a la liste des recettes </a>
    </div>
    <p>Nombre de recettes éxistantes: <?= $recipeCount ?></p>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="success">Recipe added successfully!</div>
    <?php endif; ?>

    <form method="post">
        <div class="columns">
            <div class="column">
                <div class="form-group">
                    <label for="name">Nom de la recette (Anglais)*</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="nameFR">Nom de la recette (Français)</label>
                    <input type="text" id="nameFR" name="nameFR">
                </div>

                <div class="form-group">
                    <label for="author">Auteur</label>
                    <input type="text" id="author" name="author">
                </div>

                <div class="form-group">
                    <label for="without">Dietary Restrictions (comma separated, e.g., "NoGluten,Vegan")</label>
                    <input type="text" id="without" name="without">
                </div>
            </div>

            <div class="column">
                <div class="form-group">
                    <label for="imageURL">Image URL</label>
                    <input type="text" id="imageURL" name="imageURL">
                </div>

                <div class="form-group">
                    <label for="originalURL">URL Recipe</label>
                    <input type="text" id="originalURL" name="originalURL">
                </div>
            </div>
        </div>

        <div class="columns">
            <div class="column">
                <div class="form-group">
                    <label for="ingredients">Ingredients (Anglais, 1 par ligne)*</label>
                    <textarea id="ingredients" name="ingredients" required></textarea>
                </div>

                <div class="form-group">
                    <label for="ingredientsFR">Ingredients (Français, 1 par ligne)</label>
                    <textarea id="ingredientsFR" name="ingredientsFR"></textarea>
                </div>
            </div>

            <div class="column">
                <div class="form-group">
                    <label for="steps">étapes (Anglais, 1 par ligne)*</label>
                    <textarea id="steps" name="steps" required></textarea>
                </div>

                <div class="form-group">
                    <label for="stepsFR">étapes (Fraçais, 1 par ligne)</label>
                    <textarea id="stepsFR" name="stepsFR"></textarea>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="timers">Step Timers in minutes (comma separated, e.g., "5,10,15")</label>
            <input type="text" id="timers" name="timers">
        </div>

        <button type="submit">Ajouter</button>
    </form>

</body>

</html>