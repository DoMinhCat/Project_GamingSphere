<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

$user_id = intval($_SESSION['user_id']);

$sql = "
    SELECT 
        p.id_pari, p.montant, p.cote, p.statut, p.gain, 
        t.nom_tournoi, t.type,
        e.nom AS equipe_nom,
        u.pseudo AS joueur_pseudo
    FROM paris p
    JOIN tournoi t ON p.id_tournoi = t.id_tournoi
    LEFT JOIN equipe e ON (t.type = 'equipe' AND p.id_equipe = e.id_equipe)
    LEFT JOIN utilisateurs u ON (t.type != 'equipe' AND p.id_joueur = u.id_utilisateurs)
    WHERE p.id_utilisateur = ?
    ORDER BY p.id_pari DESC
";

$stmt = $bdd->prepare($sql);
$stmt->execute([$user_id]);
$mes_paris = $stmt->fetchAll(PDO::FETCH_ASSOC);

$title = 'Mes Paris';
$pageCategory = 'mes_paris';
echo "<script>const pageCategory = '$pageCategory';</script>";
?>

<!DOCTYPE html>
<html lang="fr">
<?php require('../include/head.php'); ?>
<body>
    <?php include('../include/header.php'); ?>

    <div class="container py-5">
        <div class="card shadow rounded-4">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h2 class="mb-0 background-color: #ff6e40 !important;">📋 Mes Paris</h2>
            </div>
            <div class="card-body">

                <?php if (empty($mes_paris)): ?>
                    <div class="alert alert-info">
                        Vous n'avez encore passé aucun pari.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th>Tournoi</th>
                                    <th>Type</th>
                                    <th>Choix</th>
                                    <th>Montant</th>
                                    <th>Cote</th>
                                    <th>Statut</th>
                                    <th>Gain</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($mes_paris as $pari): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pari['nom_tournoi']) ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark"><?= htmlspecialchars($pari['type']) ?></span>
                                        </td>
                                        <td>
                                            <?= $pari['type'] === 'equipe'
                                                ? htmlspecialchars($pari['equipe_nom'] ?? 'Inconnu')
                                                : htmlspecialchars($pari['joueur_pseudo'] ?? 'Inconnu') ?>
                                        </td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($pari['montant']) ?> 🪙</span></td>
                                        <td><?= htmlspecialchars($pari['cote']) ?></td>
                                        <td>
                                            <?php 
                                            switch ($pari['statut']) {
                                                case 'gagné': 
                                                    echo '<span class="badge bg-success">Gagné</span>'; 
                                                    break;
                                                case 'perdu': 
                                                    echo '<span class="badge bg-danger">Perdu</span>'; 
                                                    break;
                                                case 'en attente': 
                                                    echo '<span class="badge bg-warning text-dark">En attente</span>'; 
                                                    break;
                                                default:
                                                    echo htmlspecialchars($pari['statut']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?= $pari['gain'] > 0 
                                                ? '<span class="text-success fw-bold">+' . htmlspecialchars($pari['gain']) . ' 🪙</span>' 
                                                : '<span class="text-muted">-</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="text-end mt-4">
                    <a href="paris-main.php" class="btn btn-outline-primary">
                        ⬅ Retour aux paris
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
