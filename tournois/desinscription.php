<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
include('../include/database.php');
require_once __DIR__ . '/../path.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour vous désinscrire.']);
    exit();
}
if (!isset($_POST['id_tournoi']) || empty($_POST['id_tournoi'])) {
    echo json_encode(['success' => false, 'message' => 'ID du tournoi manquant.']);
    exit();
}
$id_tournoi = intval($_POST['id_tournoi']);
$user_id = intval($_SESSION['user_id']);
try {
    $stmt = $bdd->prepare("DELETE FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
    $stmt->execute([$id_tournoi, $user_id]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Désinscription réussie.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas inscrit à ce tournoi.']);
    }
    exit();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la désinscription : ' . htmlspecialchars($e->getMessage())]);
    exit();
}
