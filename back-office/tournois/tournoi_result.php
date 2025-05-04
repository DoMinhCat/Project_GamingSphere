<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

if (isset($_GET['id_tournoi'])) {
    $id_tournoi = intval($_GET['id_tournoi']);
    try {
        $stmt = $bdd->prepare("SELECT * FROM tournoi WHERE id_tournoi = ?");
        $stmt->execute([$id_tournoi]);
        $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$tournoi) {
            header('Location:' . tournois_back . '?error=no_id');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . tournois_back . '?error=db');
        exit();
    }
} else {
    header('Location:' . tournois_back . '?error=no_id');
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
    $updateStatus = $bdd->prepare("UPDATE tournoi SET status_ENUM = 'Terminé' WHERE id_tournoi = ?");
    $updateStatus->execute([$id_tournoi]);

    header('Location:' . tournois_back . "?updated=1");
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
    <?php
    $page = tournois_back;
    include('../navbar.php'); ?>

    <div class="container mb-5">
        <?php if (isset($_GET['error']) && $_GET['error'] === 'result') {
            $noti_Err = 'Erreur lors de la récuperation des résultats : ' . $_SESSION['error'];
            unset($_SESSION['error']);
        }
        ?>

        <?php if (!empty($noti_Err)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $noti_Err ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif ?>

        <h1 class="my-5 text-center">Résultats du Tournoi : <?= htmlspecialchars($tournoi['nom_tournoi']); ?></h1>
        <div class="form-group mb-2 sticky-top pt-3 pb-2">
            <input type="text" id="search_results" class="form-control searchBoxBack" placeholder="Rechercher par nom des gagnants">
        </div>

        <form method="post">
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
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
                                SELECT e.id_equipe AS participant_id, e.nom AS nom_equipe, 
                                       COALESCE(r.result_id, 0) AS result_id,
                                       COALESCE(r.position, '') AS position,
                                       COALESCE(r.credits_awarded, 0) AS credits_awarded
                                FROM equipes_tournois et
                                JOIN equipe e ON et.id_equipe = e.id_equipe
                                LEFT JOIN tournament_results r ON r.tournament_id = ? AND r.team_id = e.id_equipe
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
                            $_SESSION['error'] = htmlspecialchars($e->getMessage());
                            header('Location:' . tournois_result_back . '?id_tournois=' . $id_tournoi . '&error=result');
                            exit();
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-center">
                                <button type="submit" class="btn btn-success">Enregistrer les changements</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
    </div>
</body>

</html>