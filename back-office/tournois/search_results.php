<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'], $_GET['id_tournoi'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}

$search = trim($_GET['search']);
$id_tournoi = intval($_GET['id_tournoi']);
$type = $_GET['type'] ?? 'solo'; // ajouté dynamiquement côté JS plus tard

try {
    if (strtolower($type) === 'solo') {
        $sql = "
            SELECT u.id_utilisateurs AS participant_id, u.pseudo, 
                   COALESCE(r.result_id, 0) AS result_id,
                   COALESCE(r.position, '') AS position,
                   COALESCE(r.credits_awarded, 0) AS credits_awarded
            FROM inscription_tournoi it
            JOIN utilisateurs u ON it.user_id = u.id_utilisateurs
            LEFT JOIN tournament_results r ON r.tournament_id = ? AND r.user_id = u.id_utilisateurs
            WHERE it.id_tournoi = ? AND u.pseudo LIKE :search
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
            LEFT JOIN tournament_results r ON r.tournament_id = ? AND r.team_id = e.id_equipe
            WHERE et.id_tournoi = ? AND e.nom LIKE :search
            ORDER BY r.position ASC, e.nom ASC
        ";
    }

    $stmt = $bdd->prepare($sql);
    $stmt->execute([$id_tournoi, $id_tournoi, '%' . $search . '%']);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($participants) > 0) {
        foreach ($participants as $participant) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($participant['result_id'] > 0 ? $participant['result_id'] : $participant['participant_id']) . '</td>';
            echo '<td>' . htmlspecialchars($participant['pseudo'] ?? $participant['nom_equipe']) . '</td>';
            echo '<td><input type="number" name="position[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['position']) . '" class="form-control" min="1" required></td>';
            echo '<td><input type="number" name="credits[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['credits_awarded']) . '" class="form-control" min="0" required></td>';
            echo '<input type="hidden" name="result_id[' . $participant['participant_id'] . ']" value="' . htmlspecialchars($participant['result_id']) . '">';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>Aucun participant trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='4'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
