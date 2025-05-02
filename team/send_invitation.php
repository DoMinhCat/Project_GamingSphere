<?php
session_start();
require('../include/database.php');
$login_page = '../connexion/login.php';
require_once __DIR__ . '/../path.php';
require('../include/check_session.php');

$teamId = $_POST['team_id'] ?? null;
$userId = $_SESSION['user_id'];

if ($teamId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM invitations WHERE id_equipe = ? AND id_utilisateur = ? AND statut = 'en attente'");
        $stmt->execute([$teamId, $userId]);
        $isInvitationPending = $stmt->fetchColumn() > 0;

        if ($isInvitationPending) {
            header('Location:' . team_details . '?id_equipe=' . $teamId . '&error=invitation_already_pending');
            exit();
        }
        $stmt = $bdd->prepare("INSERT INTO invitations (id_equipe, id_utilisateur, statut, date_invitation) VALUES (?, ?, 'en attente', NOW())");
        $stmt->execute([$teamId, $userId]);
        header('Location:' . team_details . '?id_equipe=' . $teamId . '&success=invitation_sent');
        exit();
    } catch (PDOException $e) {
        header('Location:' . team_details . '?id_equipe=' . $teamId . '&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location:' . team_list . '?error=missing_team_id');
    exit();
}
