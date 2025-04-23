<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/database.php');
require_once __DIR__ . '/../path.php';
require('../include/check_session.php');

$invitationId = $_POST['invitation_id'] ?? null;

if ($invitationId) {
    try {
        $stmt = $bdd->prepare("UPDATE invitations SET statut = 'acceptée' WHERE id_invitation = ?");
        $stmt->execute([$invitationId]);
        $stmt = $bdd->prepare("
            INSERT INTO membres_equipe (id_equipe, id_utilisateur, role, date_rejoint)
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
