<?php
session_start();
require('../include/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion/login.php');
    exit();
}

$invitationId = $_POST['invitation_id'] ?? null;

if ($invitationId) {
    try {
        
        $stmt = $bdd->prepare("UPDATE invitations SET statut = 'refusée' WHERE id_invitation = ?");
        $stmt->execute([$invitationId]);
        $stmt = $bdd->prepare("
            DELETE membres_equipe (id_equipe, id_utilisateur, role, date_rejoint)
            SELECT id_equipe, id_utilisateur, 'joueur', NOW()
            FROM invitations
            WHERE id_invitation = ?
        ");
        $stmt->execute([$invitationId]);

        header('Location: ../profil/my_account.php?success=team_request_accepted');
        exit();
    } catch (PDOException $e) {
        header('Location: ../profil/my_account.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}