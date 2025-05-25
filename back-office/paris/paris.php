<?php
session_start();
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

$title = 'Gestion des cotes des tournois';

// Récupérer la liste des tournois
$tournois = $bdd->query("
    SELECT id_tournoi, nom_tournoi, jeu, type, pari_ouvert, date_debut, date_fin
    FROM tournoi WHERE status_ENUM = 'en cours'
    ORDER BY date_debut DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les vainqueurs depuis tournament_results
$vainqueurs = [];
$tournoi_ids = array_column($tournois, 'id_tournoi');
if (count($tournoi_ids) > 0) {
    $in = implode(',', array_fill(0, count($tournoi_ids), '?'));
    $stmt = $bdd->prepare("
        SELECT tournament_id, user_id, team_id
        FROM tournament_results
        WHERE position = 1 AND tournament_id IN ($in)
    ");
    $stmt->execute($tournoi_ids);
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $vainqueurs[$row['tournament_id']] = $row;
    }
}

$participants_par_tournoi = [];
foreach ($tournois as $tournoi) {
    if ($tournoi['type'] === 'equipe') {
        $stmt = $bdd->prepare("
            SELECT t.id_equipe AS id_team, t.nom AS nom_team, cp.cote
            FROM inscription_tournoi it
            JOIN equipe t ON it.id_team = t.id_equipe
            LEFT JOIN cote_participant cp ON cp.id_tournoi = it.id_tournoi AND cp.id_team = it.id_team
            WHERE it.id_tournoi = ?
        ");
        $stmt->execute([$tournoi['id_tournoi']]);
        $participants_par_tournoi[$tournoi['id_tournoi']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else { // solo
        $stmt = $bdd->prepare("
            SELECT u.id_utilisateurs AS id_team, u.pseudo AS nom_team, cp.cote
            FROM inscription_tournoi it
            JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
            LEFT JOIN cote_participant cp ON cp.id_tournoi = it.id_tournoi AND cp.id_team = it.user_id
            WHERE it.id_tournoi = ?
        ");
        $stmt->execute([$tournoi['id_tournoi']]);
        $participants_par_tournoi[$tournoi['id_tournoi']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php require('../head.php'); ?>

<body>
    <?php
    $page = index_back;
    include('../navbar.php'); ?>

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
                        <th>Participants & Cotes</th>
                        <th>Pariable</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Résultat</th>
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
                            <td><?= htmlspecialchars($tournoi['id_tournoi']) ?></td>
                            <td><?= htmlspecialchars($tournoi['nom_tournoi']) ?></td>
                            <td><?= htmlspecialchars($tournoi['jeu']) ?></td>
                            <td><?= htmlspecialchars($tournoi['type']) ?></td>
                            <td>
                                <table class="w-100">
                                    <?php foreach ($participants_par_tournoi[$tournoi['id_tournoi']] as $participant): ?>
                                        <form method="post" action="edit_cote.php">
                                            <tr>
                                                <td style="width:40%;">
                                                    <?= htmlspecialchars($participant['nom_team']) ?>
                                                    <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                                                    <input type="hidden" name="id_team" value="<?= $participant['id_team'] ?>">
                                                </td>
                                                <td style="width:30%;">
                                                    <input type="number" step="0.01" min="1" name="cote"
                                                        value="<?= htmlspecialchars($participant['cote'] ?? 1) ?>"
                                                        class="form-control form-control-sm w-100" style="min-width:80px;max-width:120px;">
                                                </td>
                                                <td style="width:30%;">
                                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="min-width:80px;">Enregistrer</button>
                                                </td>
                                            </tr>
                                        </form>
                                    <?php endforeach; ?>
                                </table>
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
                            <td>
                                <?php
                                $vainqueur = $vainqueurs[$tournoi['id_tournoi']] ?? null;
                                if ($tournoi['pari_ouvert'] == 0 && !$vainqueur): ?>
                                    <form method="post" action="valider_resultats.php" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="id_tournoi" value="<?= $tournoi['id_tournoi'] ?>">
                                        <select name="id_gagnant" class="form-select form-select-sm" required>
                                            <?php foreach ($participants_par_tournoi[$tournoi['id_tournoi']] as $participant): ?>
                                                <option value="<?= $participant['id_team'] ?>">
                                                    <?= htmlspecialchars($participant['nom_team']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn btn-success btn-sm">Valider</button>
                                    </form>
                                <?php elseif ($vainqueur): ?>
                                    <span class="badge bg-success">
                                        <?php
                                        foreach ($participants_par_tournoi[$tournoi['id_tournoi']] as $participant) {
                                            if (
                                                ($vainqueur['team_id'] && $participant['id_team'] == $vainqueur['team_id']) ||
                                                ($vainqueur['user_id'] && $participant['id_team'] == $vainqueur['user_id'])
                                            ) {
                                                echo htmlspecialchars($participant['nom_team']);
                                                break;
                                            }
                                        }
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>