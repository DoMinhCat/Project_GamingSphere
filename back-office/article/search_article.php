<?php
require('../../include/database.php');
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}

$search = trim($_GET['search'] ?? '');

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT titre FROM news WHERE titre LIKE :search ");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt = $bdd->query("SELECT titre, date_article FROM news");
    }

    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($articles) > 0) {
        foreach ($articles as $article) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($article['titre']) . "</td>";
            echo "<td>" . htmlspecialchars($article['date_article']) . "</td>";
            echo '<td>
            <a href=' . article_edit_back . '?id=' . $article['id_news'] . ' class="btn btn-warning mb-1">Modifier</a>
            <a href=' . article_back . '?delete_id=' . $article['id_news'] . ' class="btn btn-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cet article ?\')">Supprimer</a>
            </td>';
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Aucun article trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
