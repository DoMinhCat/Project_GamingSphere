<?php
session_start();
require_once('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// Check if it's an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    exit('Direct access not allowed');
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

if (empty($category)) {
    echo '<div class="alert alert-danger text-center py-4"><p class="mb-0">Catégorie non précisée.</p></div>';
    exit;
}

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? AND (auteur LIKE ? OR titre LIKE ?) ORDER BY date_creation DESC");
        $stmt->execute([$category, '%' . $search . '%', '%' . $search . '%']);
    } else {
        $stmt = $bdd->prepare("SELECT * FROM forum_sujets WHERE categories = ? ORDER BY date_creation DESC");
        $stmt->execute([$category]);
    }

    $sujets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($sujets) === 0) {
        if (!empty($search)) {
            echo '<div class="alert alert-info text-center py-4">';
            echo '<p class="mb-0">Aucun sujet trouvé pour "' . htmlspecialchars($search) . '".</p>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info text-center py-4">';
            echo '<p class="mb-0">Aucun sujet dans cette catégorie pour le moment.</p>';
            echo '</div>';
        }
    } else {
        foreach ($sujets as $sujet) {
            try {
                $stmt_reponses = $bdd->prepare("SELECT COUNT(*) FROM forum_reponses WHERE id_sujet = ?");
                $stmt_reponses->execute([$sujet['id_sujet']]);
                $nb_reponses = $stmt_reponses->fetchColumn();
            } catch (PDOException) {
                $nb_reponses = 0; // Default fallback
            }

            // Generate the exact same HTML structure as in the main file
            echo '<a href="' . sujet . '?id=' . $sujet['id_sujet'] . '&category=' . urlencode($category) . '" class="text-decoration-none forumBlockLink">';
            echo '<div class="card mx-0 mb-3">';
            echo '<div class="card-body">';
            echo '<div class="d-flex justify-content-between align-items-start">';
            echo '<div class="flex-grow-1">';
            echo '<h5 class="mb-2">';
            echo htmlspecialchars($sujet['titre']);
            echo '</h5>';
            echo '<p class="text-muted mb-1 small">';
            echo 'Posté le ' . date("d/m/Y à H:i", strtotime($sujet['date_creation'])) . ' ';
            echo 'par ' . htmlspecialchars($sujet['auteur'] ?? 'Anonyme');
            echo '</p>';
            echo '</div>';
            echo '<div class="text-end ms-3">';
            echo '<span class="badge bg-primary rounded-pill">';
            echo $nb_reponses . ' réponse' . ($nb_reponses != 1 ? 's' : '');
            echo '</span>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
        }
    }
} catch (PDOException) {
    echo '<div class="alert alert-danger text-center py-4">';
    echo '<p class="mb-0">Erreur de la base de données, veuillez réessayer plus tard.</p>';
    echo '</div>';
}
