<?php
session_start();
require('../include/database.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

$teamId = $_POST['team_id'] ?? null;
$userId = $_SESSION['user_id'];

if ($teamId) {
    try {
        // Vérifiez si l'utilisateur est membre de l'équipe
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
        $stmt->execute([$teamId, $userId]);
        $isMember = $stmt->fetchColumn() > 0;

        if (!$isMember) {
            header('Location: team_details.php?id_equipe=' . $teamId . '&error=not_a_member');
            exit();
        }

        // Supprimer l'utilisateur de l'équipe
        $stmt = $bdd->prepare("DELETE FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
        $stmt->execute([$teamId, $userId]);
        header('Location: team_details.php?success=left_team&id_equipe=' . $teamId);
        exit();
    } catch (PDOException $e) {
        header('Location: team_details.php?id_equipe=' . $teamId . '&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: team_list.php?error=missing_team_id');
    exit();
}
?>
