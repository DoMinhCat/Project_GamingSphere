<?php
require('../../../include/database.php');
require_once __DIR__ . '/../../../path.php';
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
        $stmt = $bdd->prepare("SELECT id_sujet,titre, date_creation, auteur from forum_sujets where categories='Support' and (titre LIKE :search or auteur LIKE :search) order by date_creation desc;");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt = $bdd->query("SELECT id_sujet,titre, date_creation, auteur from forum_sujets where categories='Support' order by date_creation desc;");
    }

    $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($annonces) > 0) {
        foreach ($annonces as $annonce) {
            echo '<tr>
                        <td class="align-middle">' . htmlspecialchars($annonce['id_sujet']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['titre']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['date_creation']) . '</td>
                        <td class="align-middle">' . htmlspecialchars($annonce['auteur']) . '</td>
                        <td>
                            <a href=' . support_edit_back . '?id=' . $annonce['id_sujet'] . ' class="btn btn-sm btn-warning my-1 me-1">Modifier</a>
                            <button type="button" class="btn btn-sm btn-danger my-1 me-1" data-bs-toggle="modal" data-bs-target="#modal' . $annonce['id_sujet'] . '">Supprimer</button>';
            echo '<div class="modal fade" id="modal' . $annonce['id_sujet'] . '" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h1 class="modal-title fs-5">Confirmation</h1>
                                </div>
                                <div class="modal-body">
                                  Êtes-vous sûr de vouloir supprimer ce sujet de support ?
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                  <a href="' . forum_support_back . '?delete_id=' . $annonce['id_sujet'] . '" class="btn btn-danger">Supprimer</a>
                                </div>
                              </div>
                            </div>
                          </div>';
            echo '</td>
                    </tr>';
        }
    } else {
        echo "<tr><td colspan='6'>Aucun sujet trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
