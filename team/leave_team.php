<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/database.php');
require_once __DIR__ . '/../path.php';
require('../include/check_session.php');

$teamId = $_POST['team_id'] ?? null;
$userId = $_SESSION['user_id'];

if ($teamId) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
        $stmt->execute([$teamId, $userId]);
        $isMember = $stmt->fetchColumn() > 0;

        if (!$isMember) {
            header('Location:' . team_details . '?id_equipe=' . $teamId . '&error=not_a_member');
            exit();
        }
        $stmt = $bdd->prepare("DELETE FROM membres_equipe WHERE id_equipe = ? AND id_utilisateur = ?");
        $stmt->execute([$teamId, $userId]);
        header('Location:' . team_details . '?success=left_team&id_equipe=' . $teamId);
        exit();
    } catch (PDOException $e) {
        header('Location:' . team_details . '?id_equipe=' . $teamId . '&error=' . urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location:' . team_list . '?error=missing_team_id');
    exit();
}
