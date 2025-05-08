<?php
$jsonString = @file_get_contents("recipes.json");
if ($jsonString === false) {
    die("Erreur: Impossible de lire recipes.json");
}
$data = json_decode($jsonString, true);
if (!is_array($data)) {
    die("Erreur: recipes.json est mal formé");
}

$recipeName = isset($_GET['name']) ? urldecode($_GET['name']) : null;
$selectedRecipe = null;
$recipeIndex = null;

foreach ($data as $index => $recipe) {
    if ($recipe['name'] === $recipeName) {
        $selectedRecipe = $recipe;
        $recipeIndex = $index;
        break;
    }
}

$loginData = file_exists('LOGIN.json') ? json_decode(file_get_contents('LOGIN.json'), true) : [];
$currUser = null;
foreach ($loginData as $user) {
    if ($user['actif'] === true) {
        $currUser = $user;
        break;
    }
}

function ajouterCommentaire($message, $postId, $userName)
{
    $commentsFile = 'CommentRecips.json';
    $comments = [];

    if (!file_exists($commentsFile)) {
        if (!file_put_contents($commentsFile, json_encode([]))) {
            return false;
        }
    }

    $jsonString = @file_get_contents($commentsFile);
    if ($jsonString === false) {
        return false;
    }
    $comments = json_decode($jsonString, true);
    if (!is_array($comments)) {
        $comments = [];
    }

    if (!isset($comments[$postId])) {
        $comments[$postId] = [];
    }

    $nouveauCommentaire = [
        'userName' => $userName,
        'message' => $message
    ];

    $comments[$postId][] = $nouveauCommentaire;

    if (!file_put_contents($commentsFile, json_encode($comments, JSON_PRETTY_PRINT))) {
        return false;
    }

    return $nouveauCommentaire;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    if (!$selectedRecipe) {
        echo json_encode([
            'success' => false,
            'error' => 'Recette non trouvée'
        ]);
        exit;
    }

    $message = $_POST['message'] ?? null;
    $postId = $_POST['postId'] ?? null;
    $userName = $_POST['userName'] ?? null;

    if ($message && $postId !== null && $userName) {
        $comment = ajouterCommentaire($message, $postId, $userName);
        if ($comment === false) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur '
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'comment' => $comment
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Données invalides'
        ]);
    }
    exit;
}

if (!$selectedRecipe) {
    die("Recette non trouvee.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= htmlspecialchars($selectedRecipe['name']) ?></title>
    <link rel="stylesheet" href="ajouteRecette.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="tout">
        <div class="header">
            <h1>MyCuisine</h1>
            <ul>
                <li><a href="site.php">Retour à la liste des recettes</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="column middle">
                <h2><?= htmlspecialchars($selectedRecipe['name']) ?></h2>
                <img src="<?= htmlspecialchars($selectedRecipe['imageURL']) ?>" alt="<?= htmlspecialchars($selectedRecipe['name']) ?>" style="width:100%;max-width:500px;">
                <h3>Ingrédients</h3>
                <ul>
                    <?php foreach ($selectedRecipe['ingredients'] as $ingredient): ?>
                        <li><?= htmlspecialchars($ingredient['quantity']) ?> <?= htmlspecialchars($ingredient['name']) ?></li>
                    <?php endforeach; ?>
                </ul>
                <h3>Étapes</h3>
                <ol>
                    <?php foreach ($selectedRecipe['steps'] as $step): ?>
                        <li><?= htmlspecialchars($step) ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <h4>Commentaires</h4>

        <form class="forme" onsubmit="toto1(); return false;">
            <div class="Com">
                <label for="Commentaires"></label>
                <input type="text" name="message" id="Commentaires" required />
            </div>
            <div class="Envoie">
                <button type="submit">Envoyer</button>
                <p id="creation"></p>
            </div>
        </form>

        <div id="commentaires-list">
            <?php
            $comments = file_exists('CommentRecips.json') ? json_decode(file_get_contents('CommentRecips.json'), true) : [];
            $recipeComments = $comments[$recipeIndex] ?? [];
            foreach ($recipeComments as $comment) {
                if (isset($comment['userName']) && isset($comment['message'])) {
                    echo '<p class="commentaire">' . htmlspecialchars($comment['userName']) . ' : ' . htmlspecialchars($comment['message']) . '</p>';
                } elseif (isset($comment['IciLeNom'])) {
                    echo '<p class="commentaire">' . htmlspecialchars($comment['IciLeNom']) . '</p>';
                }
            }
            ?>
        </div>
    </div>

    <script>
        function toto1() {
            let commentaire = $("#Commentaires").val().trim();
            let postId = '<?= $recipeIndex ?>';
            let userName = '<?= htmlspecialchars(($currUser['prenom'] ?? '') . ' ' . ($currUser['nom'] ?? '')) ?>';

            if (!commentaire) {
                $("#creation").html("<span class='ko'>Le commentaire ne peut pas être vide.</span>");
                setTimeout(() => $("#creation").html(""), 2000);
                return;
            }

            if (!postId) {
                $("#creation").html("<span class='ko'>Erreur: ID de la recette manquant.</span>");
                setTimeout(() => $("#creation").html(""), 2000);
                return;
            }

            if (!userName.trim()) {
                $("#creation").html("<span class='ko'>Erreur: Utilisateur non connecté.</span>");
                setTimeout(() => $("#creation").html(""), 2000);
                return;
            }

            $.ajax({
                method: "POST",
                url: "recipe.php?name=<?= urlencode($recipeName) ?>",
                data: {
                    message: commentaire,
                    postId: postId,
                    userName: userName
                },
                dataType: "json"
            }).done(function(response) {
                if (response.success) {
                    $("#commentaires-list").append('<p class="commentaire">' + response.comment.userName + ' : ' + response.comment.message + '</p>');
                    $("#Commentaires").val("");
                } else {
                    $("#creation").html("<span class='ko'>Erreur: ' + response.error + '</span>");
                    setTimeout(() => $("#creation").html(""), 2000);
                }
            }).fail(function() {
                $("#creation").html("<span class='ko'>Erreur lors de l\'ajout du commentaire.</span>");
                setTimeout(() => $("#creation").html(""), 2000);
            });
        }
    </script>
</body>

</html>