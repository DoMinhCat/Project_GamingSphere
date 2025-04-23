<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');

$id_utilisateur = $_SESSION['user_id'];

// On vérifie bien 'id' comme reçu dans l'URL (ex: ?id=2)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de jeu invalide.']);
    exit();
}

$id_jeu = intval($_GET['id']);

$stmt = $bdd->prepare("SELECT * FROM jeu WHERE id_jeu = ?");
$stmt->execute([$id_jeu]);
$jeu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jeu) {
    echo json_encode(['status' => 'error', 'message' => 'Jeu introuvable.']);
    exit();
}

$stmt = $bdd->prepare("SELECT * FROM panier WHERE id_utilisateur = ? AND id_jeu = ?");
$stmt->execute([$id_utilisateur, $id_jeu]);
if ($stmt->fetch()) {
    echo json_encode(['status' => 'error', 'message' => 'Ce jeu est déjà dans votre panier.']);
    exit();
}

$stmt = $bdd->prepare("INSERT INTO panier (id_utilisateur, id_jeu) VALUES (?, ?)");
if ($stmt->execute([$id_utilisateur, $id_jeu])) {
    echo json_encode(['status' => 'success', 'message' => 'Le jeu a été ajouté à votre panier : ' . htmlspecialchars($jeu['nom'])]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout au panier.']);
}
?>
