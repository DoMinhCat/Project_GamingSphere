<?php
session_start();
require('../include/database.php');
require('../include/check_timeout.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

// Vérifiez si une équipe est spécifiée
$teamId = $_POST['team_id'] ?? null;
$userId = $_SESSION['user_id'];

if (!$teamId) {
    header('Location: ../team/team_details.php?id_equipe=' . urlencode($teamId) . '&error=missing_team');
    exit();
}

try {
    // Vérifiez si l'utilisateur a déjà rejoint l'équipe
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
    $stmt->execute([$teamId, $userId]);
    $isMember = $stmt->fetchColumn() > 0;

    if ($isMember) {
        header('Location: ../team/team_details.php?id_equipe=' . urlencode($teamId) . '&error=already_member');
        exit();
    }

    // Vérifiez si une invitation existe déjà
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM invitations WHERE id_equipe = ? AND id_utilisateur = ?");
    $stmt->execute([$teamId, $userId]);
    $invitationExists = $stmt->fetchColumn() > 0;

    if ($invitationExists) {
        header('Location: ../team/team_details.php?id_equipe=' . urlencode($teamId) . '&error=invitation_exists');
        exit();
    }

    // Envoyez l'invitation
    $stmt = $bdd->prepare("INSERT INTO invitations (id_equipe, id_utilisateur, date_invitation) VALUES (?, ?, NOW())");
    $stmt->execute([$teamId, $userId]);

    header('Location: ../team/team_details.php?id_equipe=' . urlencode($teamId) . '&success=invitation_sent');
    exit();
} catch (PDOException $e) {
    header('Location: ../team/team_details.php?id_equipe=' . urlencode($teamId) . '&error=' . urlencode($e->getMessage()));
    exit();
}