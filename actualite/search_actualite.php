<?php
require('../include/database.php');
require_once __DIR__ . '/../path.php';

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    http_response_code(403);
    exit('Accès non-autorisé');
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$origin_category = $_GET['origin_category'] ?? $category;

if (empty($category)) {
    echo '<div class="alert alert-danger text-center py-4"><p class="mb-0">Catégorie non précisée.</p></div>';
    exit;
}

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs WHERE (titre LIKE :search OR pseudo LIKE :search) AND category=:category ORDER BY date_article DESC");
        $stmt->execute(['search' => '%' . $search . '%', 'category' => $category]);
    } else {
        $stmt = $bdd->prepare("SELECT id_news, category, titre, date_article, contenue, pseudo from news join utilisateurs on auteur=utilisateurs.id_utilisateurs where category = ? ORDER BY date_article DESC");
        $stmt->execute([$category]);
    }

    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($articles) === 0) {
        if (!empty($search)) {
            echo '<div class="alert alert-info text-center py-4">';
            echo '<p class="mb-0">Aucun article trouvé pour "' . htmlspecialchars($search) . '".</p>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info text-center py-4">';
            echo '<p class="mb-0">Aucun article dans cette catégorie pour le moment.</p>';
            echo '</div>';
        }
    } else {
        foreach ($articles as $article) {
            echo '<div class="mb-3">';
            echo '<a href="actualite_article.php?id=' . $article['id_news'] . '&category=' . urlencode($origin_category) . '" class="articleBlockLink text-dark">';
            echo '<div class="article border rounded p-3 shadow-sm">';
            echo '<h2>' . htmlspecialchars($article['titre']) . '</h2>';
            echo '<p class="mb-0">' . htmlspecialchars($article['date_article']) . ' par <strong>' . htmlspecialchars($article['pseudo']) . '</strong></p>';
            echo '<p>' . nl2br(htmlspecialchars($article['contenue'])) . '</p>';
            echo '</div>';
            echo '</a>';
            echo '</div>';
        }
    }
} catch (PDOException) {
    echo '<div class="alert alert-danger text-center py-4">';
    echo '<p class="mb-0">Erreur de la base de données, veuillez réessayer plus tard.</p>';
    echo '</div>';
}
