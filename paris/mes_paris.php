<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// On vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?message=Vous devez être connecté pour voir vos paris');
    exit();
}

$user_id = intval($_SESSION['user_id']);

// Récupère les paris de l'utilisateur avec infos sur le tournoi et le choix (équipe ou joueur)
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

    <div class="container mt-4">
        <h1 class="mb-4">Mes Paris</h1>

        <?php if (empty($mes_paris)): ?>
            <p>Vous n'avez encore passé aucun pari.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
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
                            <td><?= htmlspecialchars($pari['type']) ?></td>
                            <td>
                                <?php 
                                    if ($pari['type'] === 'equipe') {
                                        echo htmlspecialchars($pari['equipe_nom'] ?? 'Inconnu');
                                    } else {
                                        echo htmlspecialchars($pari['joueur_pseudo'] ?? 'Inconnu');
                                    }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($pari['montant']) ?></td>
                            <td><?= htmlspecialchars($pari['cote']) ?></td>
                            <td>
                                <?php 
                                // Coloration simple du statut
                                switch ($pari['statut']) {
                                    case 'gagné': 
                                        echo '<span class="text-success">Gagné</span>'; 
                                        break;
                                    case 'perdu': 
                                        echo '<span class="text-danger">Perdu</span>'; 
                                        break;
                                    case 'en attente': 
                                        echo '<span class="text-warning">En attente</span>'; 
                                        break;
                                    default:
                                        echo htmlspecialchars($pari['statut']);
                                }
                                ?>
                            </td>
                            <td><?= $pari['gain'] > 0 ? htmlspecialchars($pari['gain']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="paris.php" class="btn btn-secondary mt-3">Retour aux paris</a>
    </div>

</body>
</html>
