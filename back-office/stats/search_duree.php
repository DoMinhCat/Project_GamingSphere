<?php
require_once('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
$search = trim($_GET['search'] ?? '');

try {

    $sql = "SELECT
    email,
    SUM(CASE WHEN utt.category = 'actualite' THEN utt.duration ELSE 0 END) AS actualite,
    SUM(CASE WHEN utt.category = 'communaute' THEN utt.duration ELSE 0 END) AS communaute,
    SUM(CASE WHEN utt.category = 'credits' THEN utt.duration ELSE 0 END) AS credits,
    SUM(CASE WHEN utt.category = 'error' THEN utt.duration ELSE 0 END) AS error,
    SUM(CASE WHEN utt.category = 'forum' THEN utt.duration ELSE 0 END) AS forum,
    SUM(CASE WHEN utt.category = 'magasin' THEN utt.duration ELSE 0 END) AS magasin,
    SUM(CASE WHEN utt.category = 'message' THEN utt.duration ELSE 0 END) AS message,
    SUM(CASE WHEN utt.category = 'panier' THEN utt.duration ELSE 0 END) AS panier,
    SUM(CASE WHEN utt.category = 'profil' THEN utt.duration ELSE 0 END) AS profil,
    SUM(CASE WHEN utt.category = 'equipe' THEN utt.duration ELSE 0 END) AS equipe,
    SUM(CASE WHEN utt.category = 'tournois' THEN utt.duration ELSE 0 END) AS tournois,
    SUM(CASE WHEN utt.category = 'accueil' THEN utt.duration ELSE 0 END) AS accueil,
    SUM(utt.duration) AS total_time
FROM
    visit_duration AS utt
JOIN
    utilisateurs AS u ON utt.id_utilisateur = u.id_utilisateurs WHERE email LIKE :search
GROUP BY
    u.email
ORDER BY
    total_time DESC;";

    $stmt = $bdd->prepare($sql);

    if (!empty($search)) $stmt->bindValue(':search', '%' . $search . '%');

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        foreach ($users as $user) {
            echo '<tr>';
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['email']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['total_time']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['accueil']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['profil']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['actualite']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['communaute']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['tournois']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['equipe']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['message']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['magasin']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['credits']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['panier']) . "</td>";
            echo "<td class=\"align-middle\">" . htmlspecialchars($user['error']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='12' class=\"text-center\">Aucun utilisateur trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='12' class=\"text-center\">Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
