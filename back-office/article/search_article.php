<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
$search = trim($_GET['search'] ?? '');

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT id_news, titre, date_article FROM news WHERE titre LIKE :search ORDER BY date_article DESC");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt = $bdd->query("SELECT id_news, titre, date_article FROM news ORDER BY date_article DESC");
    }

    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($articles) > 0) {
        foreach ($articles as $article) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($article['titre']) . "</td>";
            echo "<td>" . htmlspecialchars($article['date_article']) . "</td>";
            echo '<td>';
            echo "<a href='" . article_edit_back . "?id=" . $article['id_news'] . "' class=\"btn btn-warning my-1 me-1\">Modifier</a>";
            echo "<a href='" . article_back . "?delete_id=" . $article['id_news'] . "' class=\"btn btn-danger my-1 me-1\" onclick=\"return confirm(\'Êtes-vous sûr de vouloir supprimer cet article ?\')\">Supprimer</a>";
            echo '</td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Aucun article trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
