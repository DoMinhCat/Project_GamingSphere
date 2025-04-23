<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
$login_page = '../connexion/login.php';
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');

$id_utilisateur = $_SESSION['user_id'];

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

    $stmtCount = $bdd->prepare("SELECT COUNT(*) FROM panier WHERE id_utilisateur = ?");
    $stmtCount->execute([$id_utilisateur]);
    $panierCount = $stmtCount->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'message' => 'Le jeu a été ajouté à votre panier : ' . htmlspecialchars($jeu['nom']),
        'panierCount' => $panierCount
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout au panier.']);
}
