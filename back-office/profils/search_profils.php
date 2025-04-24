<?php
require('../../include/database.php');
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}

$search = trim($_GET['search'] ?? '');

try {
    if (!empty($search)) {
        $stmt = $bdd->prepare("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs WHERE pseudo LIKE :search OR email LIKE :search OR nom LIKE :search OR prenom LIKE :search");
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt = $bdd->query("SELECT id_utilisateurs, pseudo, nom, prenom, email FROM utilisateurs");
    }

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id_utilisateurs']) . "</td>";
            echo "<td>" . htmlspecialchars($user['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['pseudo']) . "</td>";
            echo "<td>" . htmlspecialchars($user['prenom']) . "</td>";
            echo "<td>";
            echo "<a href='" . profils_edit_back . "?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-primary btn-sm'>Modifier</a> ";
            echo "<a href='delete_user.php?id=" . htmlspecialchars($user['id_utilisateurs']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet utilisateur?\");'>Supprimer</a>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>Aucun utilisateur trouvé.</td></tr>";
    }
} catch (PDOException $e) {
    echo "<tr><td colspan='6'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
