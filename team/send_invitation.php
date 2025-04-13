<?php
session_start();
require('../include/database.php');
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

$teamId = $_POST['team_id'] ?? null;
$userId = $_SESSION['user_id'];

if ($teamId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM invitations WHERE id_equipe = ? AND id_utilisateur = ? AND statut = 'en attente'");
        $stmt->execute([$teamId, $userId]);
        $isInvitationPending = $stmt->fetchColumn() > 0;

        if ($isInvitationPending) {
            header('Location: team_details.php?id_equipe=' . $teamId . '&error=invitation_already_pending');
            exit();
        }
        $stmt = $bdd->prepare("INSERT INTO invitations (id_equipe, id_utilisateur, statut, date_invitation) VALUES (?, ?, 'en attente', NOW())");
        $stmt->execute([$teamId, $userId]);
        header('Location: team_details.php?id_equipe=' . $teamId . '&success=invitation_sent');
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