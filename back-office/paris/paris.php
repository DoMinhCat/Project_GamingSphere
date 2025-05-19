<?php
session_start();
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

$title = 'Gestion des cotes des tournois';

// Récupérer la liste des tournois
$tournois = $bdd->query("
    SELECT id_tournoi, nom_tournoi, jeu, type, cote, pari_ouvert, date_debut, date_fin
    FROM tournoi where status_ENUM = 'en cours'
    ORDER BY date_debut DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php require('../head.php'); ?>

<body>
    <?php include('../navbar.php'); ?>

    <main class="container my-5">
        <h1 class="text-center mb-4">Gestion des cotes des tournois</h1>

        <?php if (isset($_GET['message']) && $_GET['message'] === 'EDIT_ME'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Cote modifiée avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tournoi</th>
                        <th>Jeu</th>
                        <th>Type</th>
                        <th>Cote</th>
                        <th>Pariable</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($tournois) > 0): ?>
                    <?php foreach ($tournois as $tournoi): ?>
                        <tr>
                            <td><?= htmlspecialchars($tournoi['id_tournoi']) ?></td>
                            <td><?= htmlspecialchars($tournoi['nom_tournoi']) ?></td>
                            <td><?= htmlspecialchars($tournoi['jeu']) ?></td>
                            <td><?= htmlspecialchars($tournoi['type']) ?></td>
                            <td>
                                <form method="post" action="edit_cote.php" class="d-flex align-items-center">
                                    <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                                    <input type="number" step="0.01" min="1" name="cote" value="<?= htmlspecialchars($tournoi['cote'] ?? 1) ?>" class="form-control form-control-sm" style="width:80px;">
                                    <button type="submit" class="btn btn-sm btn-primary ms-2">Enregistrer</button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="toggle_pariable.php" class="d-inline">
                                    <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                                    <input type="hidden" name="pari_ouvert" value="<?= $tournoi['pari_ouvert'] ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-sm <?= $tournoi['pari_ouvert'] ? 'btn-success' : 'btn-secondary' ?>">
                                        <?= $tournoi['pari_ouvert'] ? 'Oui' : 'Non' ?>
                                    </button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($tournoi['date_debut']) ?></td>
                            <td><?= htmlspecialchars($tournoi['date_fin']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucun tournoi trouvé.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>