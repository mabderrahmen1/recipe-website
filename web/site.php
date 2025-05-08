<?php
$likesData = file_exists('likes.json') ? json_decode(file_get_contents('likes.json'), true) : [];
$jsonFile = 'LOGIN.json';
$data = json_decode(file_get_contents($jsonFile), true);

$isAdmin = false;
$isChef = false;
$isTrad = false;
$currUser = null;
$lang = 'fr';

foreach ($data as $user) {
    if ($user['actif'] === true) {
        $currUser = $user;
        $isAdmin = ($currUser['role'] === 'Admin');
        $isChef = ($currUser['role'] === 'Chef' && ($currUser['status'] === 'validé'));
        $isTrad = ($currUser['role'] === 'Traducteur' && ($currUser['status'] === 'validé'));
        $lang = isset($currUser['lang']) ? $currUser['lang'] : 'fr';
        break;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>MyCuisine</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
<script>
        const translations = {
            fr: {
                "Profil": "Profil",
                "Recettes": "Recettes",
                "Nom": "Nom",
                "Prénom": "Prénom",
                "Email": "Email",
                "Rôle": "Rôle",
                "Recherche": "Recherche",
                "Ajouter une recette": "Ajouter une recette",
                "Déconnexion": "Déconnexion",
                "À propos": "À propos",
                "Contact": "Contact",
                "Modifier le profil": "Modifier le profil",
                "Chargement des recettes...": "Chargement des recettes...",
                "Chargement...": "Chargement...",
                "Erreur de chargement des recettes": "Erreur de chargement des recettes",
                "Aucune recette trouvée.": "Aucune recette trouvée."
            },
            en: {
                "Profil": "Profile",
                "Recettes": "Recipes",
                "Nom": "Name",
                "Prénom": "First Name",
                "Email": "Email",
                "Rôle": "Role",
                "Recherche": "Search",
                "Ajouter une recette": "Add a recipe",
                "Déconnexion": "Logout",
                "À propos": "About",
                "Contact": "Contact",
                "Modifier le profil": "Edit Profile",
                "Chargement des recettes...": "Loading recipes...",
                "Chargement...": "Loading...",
                "Erreur de chargement des recettes": "Failed to load recipes",
                "Aucune recette trouvée.": "No recipe found."
            }
        };

        function translatePage(lang) {
            $('[data-translate]').each(function () {
                const key = $(this).data('translate');
                $(this).text(translations[lang][key] || key);
            });

            $('#searchInput').attr('placeholder', lang === 'en' ? 'Enter recipe name' : 'Entrez le nom de la recette');
            $('.loading-spinner').text(translations[lang]['Chargement des recettes...']);
            $('.error').text(translations[lang]['Erreur de chargement des recettes']);
            $('#noResultMessage').text(translations[lang]['Aucune recette trouvée.']);
        }

        $(document).ready(function () {
            translatePage('<?= $lang ?>');

            $('#selecteurLangue').on('change', function () {
                const selectedLanguage = $(this).val();
                translatePage(selectedLanguage);

                $.ajax({
                    url: 'changerLangue.php',
                    type: 'POST',
                    data: { lang: selectedLanguage },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            console.error('Erreur de changement de langue');
                        }
                    },
                    error: function () {
                        console.error('Échec de la requête AJAX');
                    }
                });
            });
        });
    </script>

    <div class="header">
        <nav>
            <ul class="nav-links">
                <li>
                    <h1>MyCuisine</h1>
                </li>
                <li><a href="aboutus.php"data-translate="À propos"></a></li>
                <li><a href="contactus.php" data-translate="Contact"></a></li>
                <li><a href="deco.php" data-translate="Déconnexion"></a></li>
                <?php if ($isAdmin): ?>
                    <li><a type="button" class="adminButton" href="admin.php">Page Admin</a></li>
                <?php endif; ?>
                
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="column side">
            <h2 data-translate="Profil"></h2>
            <img src="https://www.pngmart.com/files/23/Profile-PNG-Photo.png" class="profileImg" alt="Profile Picture">
            <p><span data-translate="Nom">Nom</span>: <?= htmlspecialchars($currUser['nom'] ?? 'Nom') ?></p>
            <p><span data-translate="Prénom">Prénom</span>: <?= htmlspecialchars($currUser['prenom'] ?? 'Prénom') ?></p>
            <p><span data-translate="Email">Email</span>: <?= htmlspecialchars($currUser['email'] ?? 'Email') ?></p>
            <p><span data-translate="Rôle">Rôle</span>: <?= htmlspecialchars($currUser['role'] ?? 'Utilisateur') ?></p>

            <select name="langue" id="selecteurLangue" class="selecteurLangue">
                <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
                <option value="fr" <?= $lang === 'fr' ? 'selected' : '' ?>>Français</option>
            </select>
            <input type="file">
            <a type="button" class="siteButton" href="index.php" data-translate="Modifier le profil"></a>
            <a type="button" class="siteButton" href="deco.php" data-translate="Déconnexion"></a>
        </div>

        <div class="column middle">
            <h2 data-translate="Recettes" ></h2>
            <div class="imggg" id="recipesContainer">
                <div class="loading-spinner" data-translate="Chargement des recettes..." ></div>
            </div>
        </div>

        <div class="column side">
            <h2 data-translate="Recherche"></h2>
            <form id="search" class="recherche">
                <input type="text" id="searchInput" placeholder="Entrez le nom de la recette" required>
                <button type="button" id="rechercherBtn">Rechercher</button>
            </form>
        </div>
    </div>

    <?php if ($isChef || $isAdmin): ?>
        <a type="button" class="ajouteRecetteButton" href="ajouteRecette.php">+</a>
    <?php endif; ?>
    <?php if ($isAdmin || $isTrad): ?>
        <li><a type="button" class="tradButton" href="traduction.php">Traduction</a></li>
    <?php endif; ?>

    <script>
        $(document).ready(function() {
            function loadRecipes() {
                $.ajax({
                    url: 'loadrecipes.php',
                    type: 'GET',
                    dataType: 'html',
                    beforeSend: function() {
                        $('#recipesContainer').html('<div class="loading-spinner">Chargement...</div>');
                    },
                    success: function(data) {
                        $('#recipesContainer').html(data);
                    },
                    error: function() {
                        $('#recipesContainer').html('<div class="error">Erreur de chargement des recettes</div>');
                    }
                });
            }

            loadRecipes();
            setInterval(loadRecipes, 300000);

            $(document).on('click', '.post-rating-button', function(e) {
                const $button = $(this);
                const $rating = $button.closest('.post-rating');
                const $card = $button.closest('.recetteCard');
                const postId = $card.data('post-id');
                const isLike = $button.text().trim() === 'thumb_up';
                const currentState = $rating.hasClass('post-rating-selected');
                const $otherRating = $rating.siblings('.post-rating');
                const otherState = $otherRating.hasClass('post-rating-selected');

                let action;
                if (currentState) {
                    action = isLike ? 'unlike' : 'undislike';
                } else {
                    action = isLike ? 'like' : 'dislike';
                }

                $.post('likes.php', {
                    postId: postId,
                    action: action,
                    userId: '<?= htmlspecialchars($currUser['email'] ?? 'anonymous') ?>'
                }, function(data) {
                    $rating.find('.post-rating-count').text(data[isLike ? 'like' : 'dislike']);
                    $otherRating.find('.post-rating-count').text(data[isLike ? 'dislike' : 'like']);

                    if (action === 'unlike' || action === 'undislike') {
                        $rating.removeClass('post-rating-selected');
                    } else {
                        $rating.addClass('post-rating-selected');
                        $otherRating.removeClass('post-rating-selected');
                    }
                }, 'json').fail(function() {
                    console.error('Erreur ');
                });
            });

            $('#rechercherBtn').on('click', function() {
                const searchQuery = $('#searchInput').val().trim().toLowerCase();
                $('#noResultMessage').remove();

                if (searchQuery) {
                    const $recipes = $('#recipesContainer .recetteCard');
                    let found = false;

                    $recipes.each(function() {
                        const recipeTitle = $(this).find('.TitreRecette').text().trim().toLowerCase();
                        if (recipeTitle.includes(searchQuery)) {
                            $('html, body').animate({
                                scrollTop: $(this).offset().top - 100
                            }, 600);
                            $(this).addClass('highlight-recipe');
                            setTimeout(() => {
                                $(this).removeClass('highlight-recipe');
                            }, 3000);
                            found = true;
                            return false;
                        }
                    });

                    if (!found) {
                        $('#search').after('<p id="noResultMessage" style="color:red; font-weight:bold;">Aucune recette trouvée.</p>');
                    }
                }
            });

            $('#selecteurLangue').change(function() {
                var selectedLanguage = $(this).val();
                $.ajax({
                    url: 'changerLangue.php',
                    type: 'POST',
                    data: {
                        lang: selectedLanguage
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        } else {
                            console.error('Erreur de changement de langue');
                        }
                    },
                    error: function() {
                        console.error('Échec de la requête AJAX');
                    }
                });
            });
        });
    </script>
</body>

</html>