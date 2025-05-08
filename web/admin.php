<?php
$usersFile = 'LOGIN.json';
$users = json_decode(file_get_contents($usersFile), true);
$recipesFile = 'recipes.json';
$recipes = json_decode(file_get_contents($recipesFile), true);

$current = null;
foreach ($users as $user) {
    if ($user['actif'] === true) {
        $current = $user;
        $isAdmin = ($user['role'] === 'Admin');
        $isTrad = ($user['role'] === 'Traducteur' && $user['status'] === 'validé');
        break;
    }
}

if (!($isAdmin)) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['status'])) {
        foreach ($_POST['status'] as $email => $newStatus) {
            foreach ($users as &$user) {
                if ($user['email'] == $email) {
                    $user['status'] = $newStatus;
                    break;
                }
            }
        }
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        $successMessage = "Statuts mis à jour avec succès!";
    }

    if (isset($_POST['delete_recipe'])) {
        $idToDelete = $_POST['delete_recipe'];
        $recipes = array_filter($recipes, fn($r) => $r['id'] != $idToDelete);
        file_put_contents($recipesFile, json_encode(array_values($recipes), JSON_PRETTY_PRINT));
        $successMessage = "Recette supprimée avec succès!";
    }
}
?>


<!DOCTYPE html>
<head>
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<a href="site.php">retour</a>
    <h1>Panneau d'administration</h1>
    
    <?php if (isset($successMessage)): ?>
        <div class="success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    
    <form method="post">
        <table>
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td>
                            <select name="status[<?= htmlspecialchars($user['email']) ?>]">
                                <option value="en attente" <?= ($user['status'] ?? '') === 'en attente' ? 'selected' : '' ?>>En attente</option>
                                <option value="accepte" <?= ($user['status'] ?? '') === 'accepte' ? 'selected' : '' ?>>Accepté</option>
                                <option value="rejete" <?= ($user['status'] ?? '') === 'rejete' ? 'selected' : '' ?>>Rejeté</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" style="margin-top: 20px;">Enregistrer les modifications</button>
    </form>
    <h2>Recettes existantes</h2>
<form method="POST">
    <table>
        <thead>
            <tr>
                <th>Nom (EN)</th>
                <th>Nom (FR)</th>
                <th>Auteur</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $recipe): ?>
                <tr>
                    <td><?= htmlspecialchars($recipe['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recipe['nameFR'] ?? '') ?></td>
                    <td><?= htmlspecialchars($recipe['auteur'] ?? '') ?></td>
                    <td>
                        <button type="submit" name="delete_recipe" value="<?= htmlspecialchars($recipe['id']) ?>" onclick="return confirm('Supprimer cette recette ?')">Supprimer</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

</body>
</html>