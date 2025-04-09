<?php
session_start();
include('../include/database.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour participer.']);
    exit();
}

// Vérifiez si un ID de tournoi est passé
if (!isset($_POST['id_tournoi']) || empty($_POST['id_tournoi'])) {
    echo json_encode(['success' => false, 'message' => 'ID du tournoi manquant.']);
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']); // Sécurisez l'ID
$user_id = intval($_SESSION['user_id']); // ID de l'utilisateur connecté

try {
    // Vérifiez si l'utilisateur est déjà inscrit
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
    $stmt->execute([$id_tournoi, $user_id]);
    $already_registered = $stmt->fetchColumn();

    if ($already_registered > 0) {
        echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à ce tournoi.']);
        exit();
    }

    // Insérez l'inscription
    $stmt = $bdd->prepare("INSERT INTO inscription_tournoi (id_tournoi, user_id, date_inscription) VALUES (?, ?, NOW())");
    $stmt->execute([$id_tournoi, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Inscription réussie.']);
    exit();
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription : ' . htmlspecialchars($e->getMessage())]);
    exit();
}
?>