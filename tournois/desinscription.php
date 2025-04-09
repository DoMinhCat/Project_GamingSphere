<?php
session_start();
include('../include/database.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour vous désinscrire.']);
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
?>