<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$typeFilter = trim($_GET['type'] ?? '');

try {
    $searchSQL = !empty($search) ? "(nom_tournoi LIKE :search OR jeu LIKE :search)" : "1=1";
    $statusSQL = !empty($statusFilter) ? "AND status_ENUM = :status" : "";
    $typeSQL = !empty($typeFilter) ? "AND type = :type" : "";

    $sql = "SELECT id_tournoi, nom_tournoi, status_ENUM, jeu, type, date_debut, date_fin 
    FROM tournoi WHERE $searchSQL $statusSQL $typeSQL ORDER BY date_debut DESC";
    $stmt = $bdd->prepare($sql);

    if (!empty($search)) $stmt->bindValue(':search', '%' . $search . '%');
    if (!empty($statusFilter)) $stmt->bindValue(':status', $statusFilter);
    if (!empty($typeFilter)) $stmt->bindValue(':type', $typeFilter);
    $stmt->execute();
    $tournois = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tournois) > 0) {
        foreach ($tournois as $tournoi) {
            echo '<tr>';
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['id_tournoi']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['nom_tournoi']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['jeu']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['date_debut']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['date_fin']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['status_ENUM']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($tournoi['type']) . "</td>";
            echo "<td class='align-middle'>";
            echo "<div class='d-flex flex-wrap align-items-start flex-xl-row align-items-start'>";
            echo "<a href=" . tournois_edit_back . "?id_tournoi=" . $tournoi['id_tournoi'] . " class=\"btn btn-sm btn-warning mb-1 mb-xl-0 me-sm-1\">Modifier</a>";
            echo '<button type="button" class="btn btn-sm btn-danger mb-1 mb-xl-0 me-sm-1" data-bs-toggle="modal" data-bs-target="#deleteModal' . $tournoi['id_tournoi'] . '">Supprimer</button>';
            echo "<a href=" . tournois_result_back . "?id_tournoi=" . $tournoi['id_tournoi'] . " class=\"btn btn-sm btn-success\">Éditer les Résultats</a>";
            echo "</div>";
            echo '<div class="modal fade" id="deleteModal' . $tournoi['id_tournoi'] . '" tabindex="-1" aria-labelledby="deleteModalLabel' . $tournoi['id_tournoi'] . '" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="deleteModalLabel' . $tournoi['id_tournoi']  . '">Confirmation</h1>
                                                    </div>
                                                    <div class="modal-body">
                                                        Êtes-vous sûr de vouloir supprimer ce tournois ?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                        <a type="button" class="btn btn-danger" href="delete_tournois.php?id_tournoi=' . $tournoi['id_tournoi'] . '">Supprimer</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
            echo "</td>";

            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='12' class=\"text-center\">Aucun utilisateur trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='12' class=\"text-center\">Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
