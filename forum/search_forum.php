<?php
require('../include/database.php');
require_once __DIR__ . '/../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
$search = trim($_GET['search'] ?? '');
$category = $_GET['category'];

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? AND parent_id IS NULL AND (auteur LIKE ? OR titre LIKE ?) ORDER BY date_creation DESC;");
        $stmt->execute([$category, '%' . $search . '%', '%' . $search . '%']);
    } else {
        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? AND parent_id IS NULL ORDER BY date_creation DESC;");
        $stmt->execute([$category]);
    }

    $sujets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($sujets) > 0) {
        foreach ($sujets as $sujet) {
            echo '<a href="' . sujet . '?id=' . $sujet['id_sujet'] . '&category=' . $categorie_nom . '" class="text-decoration-none forumBlockLink">';

            echo '<div class="card mx-0 mb-3">
                        <div class="card-body">';
            echo '<h5>' . htmlspecialchars($sujet['titre']) . '</h5>';
            echo '<p class="text-muted mb-1">Posté le ' . date("d/m/Y à H:i", strtotime($sujet['date_creation'])) . ' par ' . htmlspecialchars($sujet['auteur'] ?? 'Anonyme') . '</p>';
            echo '<p class="mb-0"><strong>' . $nb_reponses . '</strong> ' . ($nb_reponses != 1 ? "réponses" : "réponse") . '</p>';
            echo '</div>
                    </div>
                </a>';
        }
    } else {
        echo "<p>Aucun sujet trouvé.</p>";
    }
} catch (PDOException) {
    echo "<p>Erreur de la base de données.</p>";
}
