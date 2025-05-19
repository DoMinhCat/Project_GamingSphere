<?php
session_start();
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

$title = 'Gestion des paris';

// Récupérer la liste des paris avec les infos nécessaires
$paris = $bdd->query("
    SELECT p.*, 
        t.nom_tournoi, 
        t.jeu, 
        t.type AS type_tournoi, 
        u.pseudo AS utilisateur
    FROM paris p
    LEFT JOIN tournoi t ON p.id_tournoi = t.id_tournoi
    LEFT JOIN utilisateurs u ON p.id_utilisateur = u.id_utilisateurs
    ORDER BY p.date_pari DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<?php require('../head.php'); ?>

<body>
    <?php include('../navbar.php'); ?>

    <main class="container my-5">
        <h1 class="text-center mb-4">Gestion des paris</h1>

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
                        <th>Utilisateur</th>
                        <th>Montant (€)</th>
                        <th>Cote</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($paris) > 0): ?>
                    <?php foreach ($paris as $pari): ?>
                        <tr>
                            <td><?= htmlspecialchars($pari['id_pari']) ?></td>
                            <td><?= htmlspecialchars($pari['nom_tournoi']) ?></td>
                            <td><?= htmlspecialchars($pari['jeu']) ?></td>
                            <td><?= htmlspecialchars($pari['type_tournoi']) ?></td>
                            <td><?= htmlspecialchars($pari['utilisateur']) ?></td>
                            <td><?= htmlspecialchars($pari['montant']) ?></td>
                            <td>
                                <form method="post" action="edit_cote.php" class="d-flex align-items-center">
                                    <input type="hidden" name="id_pari" value="<?= $pari['id_pari'] ?>">
                                    <input type="number" step="0.01" min="1" name="cote" value="<?= htmlspecialchars($pari['cote'] ?? 1) ?>" class="form-control form-control-sm" style="width:80px;">
                                    <button type="submit" class="btn btn-sm btn-primary ms-2">Enregistrer</button>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($pari['statut']) ?></td>
                            <td><?= htmlspecialchars($pari['date_pari']) ?></td>
                            <td>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Aucun pari trouvé.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>