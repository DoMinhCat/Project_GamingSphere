<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}

$search = trim($_GET['search']);
$prixFilter = $_GET['prix'] ?? '';

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(nom LIKE :search OR catégorie LIKE :search OR plateforme LIKE :search OR type LIKE :search OR éditeur LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}


if ($prixFilter === '0-20') {
    $where[] = "prix BETWEEN 0 AND 20";
} elseif ($prixFilter === '20-40') {
    $where[] = "prix > 20 AND prix <= 40";
} elseif ($prixFilter === '40+') {
    $where[] = "prix > 40";
}

$sql = "SELECT id_jeu, catégorie, date_sortie, image, nom, note_jeu, plateforme, prix, type, éditeur FROM jeu";

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY nom ASC';

try {
    $stmt = $bdd->prepare($sql);
    $stmt->execute($params);
    $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($games) > 0) {
        foreach ($games as $game) {
            echo '<tr>
                <td class="align-middle">' . htmlspecialchars($game['nom']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['date_sortie']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['catégorie']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['note_jeu']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['plateforme']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['prix']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['type']) . '</td>
                <td class="align-middle">' . htmlspecialchars($game['éditeur']) . '</td>
                <td>
                <div class="d-flex flex-wrap align-items-start flex-lg-row align-items-start">
                    <a href="delete_game.php?id=' . $game['id_jeu'] . '" class="btn btn-danger btn-sm mb-1 mb-lg-0 me-sm-1" onclick="return confirm(\'Voulez-vous vraiment supprimer ce jeu ?\');">Supprimer</a>
                    <button type="button" class="btn btn-sm btn-danger mb-1 mb-lg-0 me-sm-1" data-bs-toggle="modal" data-bs-target="#deleteModal' . $game['id_jeu'] . '">Supprimer</button>
                </div>
                                    <div class="modal fade" id="deleteModal' . $game['id_jeu'] . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $game['id_jeu'] . '" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel' . $game['id_jeu'] . '">Confirmation</h1>
                                                </div>
                                                <div class="modal-body">
                                                    Êtes-vous sûr de vouloir supprimer ce jeu du magasin ?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <a type="button" class="btn btn-danger" href="delete_game.php?id=' . $game['id_jeu'] . '">Supprimer</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                </td>
            </tr>';
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">Aucun jeu trouvé.</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="9" class="text-danger">Erreur : ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}
