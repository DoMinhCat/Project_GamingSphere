<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
if (isset($_GET['id_tournoi'])) {
    $id_tournoi = intval($_GET['id_tournoi']);
    try {
        $stmt = $bdd->prepare("SELECT * FROM tournoi WHERE id_tournoi = ?");
        $stmt->execute([$id_tournoi]);
        $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$tournoi) {
            echo "<div class='alert alert-danger'>Tournoi non trouvé.</div>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>Aucun ID de tournoi spécifié.</div>";
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['position'], $_POST['credits'], $_POST['result_id'])) {
    foreach ($_POST['position'] as $participant_id => $position) {
        $position = intval($position);
        $credits = intval($_POST['credits'][$participant_id]);
        $result_id = intval($_POST['result_id'][$participant_id]);
        $participant_id = intval($participant_id);

        if ($result_id > 0) {
            $updateStmt = $bdd->prepare("UPDATE tournament_results SET position = ?, credits_awarded = ? WHERE result_id = ?");
            $updateStmt->execute([$position, $credits, $result_id]);

            if (strtolower($tournoi['type']) === 'solo') {
                $updateCredits = $bdd->prepare("
                    INSERT INTO credits (user_id, credits)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE credits = credits + VALUES(credits)
                ");
                $updateCredits->execute([$participant_id, $credits]);
            }
        } else {
            if (strtolower($tournoi['type']) === 'solo') {
                $insertStmt = $bdd->prepare("INSERT INTO tournament_results (tournament_id, user_id, position, credits_awarded, team_id) VALUES (?, ?, ?, ?, NULL)");
                $insertStmt->execute([$id_tournoi, $participant_id, $position, $credits]);

                $updateCredits = $bdd->prepare("
                    INSERT INTO credits (user_id, credits)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE credits = credits + VALUES(credits)
                ");
                $updateCredits->execute([$participant_id, $credits]);
            } else {
                $insertStmt = $bdd->prepare("INSERT INTO tournament_results (tournament_id, team_id, position, credits_awarded, user_id) VALUES (?, ?, ?, ?, NULL)");
                $insertStmt->execute([$id_tournoi, $participant_id, $position, $credits]);
            }
        }
    }

    header("Location: tournois_main.php?id_tournoi=" . $id_tournoi . "&updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Éditer les Résultats du Tournoi';
require('../head.php');
?>
<body>
    <?php include('../navbar.php'); ?>

    <div class="container my-5">
        <h1 class="mb-4 text-center">Résultats du Tournoi : <?= htmlspecialchars($tournoi['nom_tournoi']); ?></h1>
        <div class="mb-4 text-end">
            <a href="tournoi_list.php" class="btn btn-primary">Retour à la Liste des Tournois</a>
        </div>
        <h3>Participants et Résultats</h3>
        <form method="post">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th><?= strtolower($tournoi['type']) === 'solo' ? 'Pseudo Joueur' : 'Nom de l\'Équipe' ?></th>
                        <th>Position</th>
                        <th>Crédits Attribués</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        if (strtolower($tournoi['type']) === 'solo') {
                            $stmt = $bdd->prepare("
                                SELECT u.id_utilisateurs AS participant_id, u.pseudo, 
                                       COALESCE(r.result_id, 0) AS result_id,
                                       COALESCE(r.position, '') AS position,
                                       COALESCE(r.credits_awarded, 0) AS credits_awarded
                                FROM inscription_tournoi it
                                JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
                                LEFT JOIN tournament_results r ON r.tournament_id = ? AND r.user_id = u.id_utilisateurs
                                WHERE it.id_tournoi = ?
                                ORDER BY r.position ASC, u.pseudo ASC
                            ");
                            $stmt->execute([$id_tournoi, $id_tournoi]);
                        } else {
                            $stmt = $bdd->prepare("
                                SELECT e.id_équipe AS participant_id, e.nom AS nom_equipe, 
                                       COALESCE(r.result_id, 0) AS result_id,
                                       COALESCE(r.position, '') AS position,
                                       COALESCE(r.credits_awarded, 0) AS credits_awarded
                                FROM equipes_tournois et
                                JOIN equipe e ON et.id_equipe = e.id_équipe
                                LEFT JOIN tournament_results r ON r.tournament_id = ? AND r.team_id = e.id_équipe
                                WHERE et.id_tournoi = ?
                                ORDER BY r.position ASC, e.nom ASC
                            ");
                            $stmt->execute([$id_tournoi, $id_tournoi]);
                        }
                        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($participants) === 0) {
                            echo "<tr><td colspan='4' class='text-center'>Aucun participant enregistré pour ce tournoi.</td></tr>";
                        } else {
                            foreach ($participants as $participant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($participant['result_id'] > 0 ? $participant['result_id'] : $participant['participant_id']) ?></td>
                                    <td><?= htmlspecialchars($participant['pseudo'] ?? $participant['nom_equipe']) ?></td>
                                    <td>
                                        <input type="number" name="position[<?= $participant['participant_id'] ?>]" value="<?= htmlspecialchars($participant['position']) ?>" class="form-control" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" name="credits[<?= $participant['participant_id'] ?>]" value="<?= htmlspecialchars($participant['credits_awarded']) ?>" class="form-control" min="0" required>
                                    </td>
                                    <input type="hidden" name="result_id[<?= $participant['participant_id'] ?>]" value="<?= htmlspecialchars($participant['result_id']) ?>">
                                </tr>
                            <?php endforeach;
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='4'>Erreur lors de la récupération des résultats : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end">
                            <button type="submit" class="btn btn-success">Enregistrer les changements</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>
    </div>
</body>
</html>
