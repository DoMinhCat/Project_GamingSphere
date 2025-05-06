<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'], $_GET['id_tournoi'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}

$search = trim($_GET['search']);
$id_tournoi = intval($_GET['id_tournoi']);
$type = $_GET['type'] ?? 'solo';

try {
    if (strtolower($type) === 'solo') {
        $sql = "
            SELECT u.id_utilisateurs AS participant_id, u.pseudo, 
                   COALESCE(r.result_id, 0) AS result_id,
                   COALESCE(r.position, '') AS position,
                   COALESCE(r.credits_awarded, 0) AS credits_awarded
            FROM inscription_tournoi it
            JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
            LEFT JOIN tournament_results r ON r.tournament_id = :id_tournoi AND r.user_id = u.id_utilisateurs
            WHERE it.id_tournoi = :id_tournoi AND u.pseudo LIKE :search
            ORDER BY r.position ASC, u.pseudo ASC
        ";
    } else {
        $sql = "
            SELECT e.id_equipe AS participant_id, e.nom AS nom_equipe, 
                   COALESCE(r.result_id, 0) AS result_id,
                   COALESCE(r.position, '') AS position,
                   COALESCE(r.credits_awarded, 0) AS credits_awarded
            FROM equipes_tournois et
            JOIN equipe e ON et.id_equipe = e.id_equipe
            LEFT JOIN tournament_results r ON r.tournament_id = :id_tournoi AND r.team_id = e.id_equipe
            WHERE et.id_tournoi = :id_tournoi AND e.nom LIKE :search
            ORDER BY r.position ASC, e.nom ASC
        ";
    }

    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':id_tournoi', $id_tournoi, PDO::PARAM_INT);
    $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $stmt->execute();
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($participants) > 0) {
        foreach ($participants as $participant) {
            echo '<tr>
                <td class="align-middle">' . htmlspecialchars($participant['result_id'] > 0 ? $participant['result_id'] : $participant['participant_id']) . '</td>
                <td class="align-middle">' . htmlspecialchars($participant['pseudo'] ?? $participant['nom_equipe']) . '</td>
                <td class="align-middle"><input type="number" name="position[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['position']) . '" class="form-control" min="1" required></td>
                <td class="align-middle"> <input type="number" name="credits[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['credits_awarded']) . '" class="form-control" min="0" required></td>
                <input type="hidden" name="result_id[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['result_id']) . '">
            </tr>';
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>Aucun participant trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
