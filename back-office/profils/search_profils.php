<?php
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
function isOnline($lastActive)
{
    $timeout = 60;
    $lastActiveTime = strtotime($lastActive);
    return (time() - $lastActiveTime) <= $timeout;
}
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT id_utilisateurs, pseudo, nom, prenom, email, last_active FROM utilisateurs WHERE pseudo LIKE :search OR email LIKE :search OR nom LIKE :search OR prenom LIKE :search ORDER BY nom ASC");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email, last_active FROM utilisateurs ORDER BY nom ASC");
    }

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        foreach ($users as $user) {
            $isUserOnline = isOnline($user['last_active']);
            if ($statusFilter === 'online' && !$isUserOnline) continue;
            if ($statusFilter === 'offline' && $isUserOnline) continue;

            echo "<tr>";

            echo "<td>" . htmlspecialchars($user['id_utilisateurs']) . "</td>";
            echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['pseudo']) . "</td>";
            echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
            echo "<td>
            <span class=\"user-status\" data-user=\"" . htmlspecialchars($user['pseudo']) . "\">" . (isOnline($user['last_active']) ? '<span style="color: green;">Online</span>' : '<span style="color: gray;">Offline</span>') . "</span>
            </td>";

            echo "<td>";
            echo "<div class='d-flex flex-wrap align-items-start flex-md-row align-items-start'>";
            echo "<a href='" . profils_edit_back . "?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-md-0 me-sm-1'>Modifier</a> ";
            echo "<a href='export_pdf.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm mb-1 mb-md-0 me-sm-1'>Exporter PDF</a> ";
            echo "<a href='delete_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-danger btn-sm mb-1 mb-md-0 me-sm-1' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur?\");'>Supprimer</a>";
            echo '</div>';
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Aucun utilisateur trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
