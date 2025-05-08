<?php
$likesData = file_exists('likes.json') ? json_decode(file_get_contents('likes.json'), true) : [];

$jsonString = file_get_contents("recipes.json");
$data = json_decode($jsonString, true);
$defaultImage = 'https://mardemzamora.com/wp-content/uploads/2021/02/products-71cmugf0b7l._ac_sl1500__2.jpg';

foreach ($data as $index => $recette) {
    if (!isset($recette['name']) || empty($recette['name'])) continue;

    $imageURL = $defaultImage;
    if (isset($recette['imageURL']) && !empty($recette['imageURL'])) {
        $imageURL = filter_var($recette['imageURL'], FILTER_VALIDATE_URL)
            ? $recette['imageURL']
            : $defaultImage;
    }

    $likeCount = $likesData[$index]['like'] ?? 0;
    $dislikeCount = $likesData[$index]['dislike'] ?? 0;

    echo '
    <div class="recetteCard" data-post-id="' . $index . '">
        <a href="recipe.php?name=' . urlencode($recette['name']) . '" class="recetteLink">
            <img src="' . htmlspecialchars($imageURL) . '" 
                 style="width:256px; height:256px; object-fit: cover; border-radius: 8px;" 
                 alt="' . htmlspecialchars($recette['name']) . '"
                 onerror="this.src=\'' . $defaultImage . '\'">
            <div class="TitreRecette">' . htmlspecialchars($recette['name']) . '</div>
        </a>
        <div class="post-ratings-container">
            <div class="post-rating">
                <span class="post-rating-button material-icons">thumb_up</span>
                <span class="post-rating-count like-count">' . $likeCount . '</span>
            </div>
            <div class="post-rating">
                <span class="post-rating-button material-icons">thumb_down</span>
                <span class="post-rating-count dislike-count">' . $dislikeCount . '</span>
            </div>
        </div>
    </div>';
}
