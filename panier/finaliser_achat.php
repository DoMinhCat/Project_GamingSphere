<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/database.php');
require('../include/check_session.php');
require_once __DIR__ . '/../path.php';

$id_utilisateur = $_SESSION['user_id'];

$stmt = $bdd->prepare("SELECT p.id_jeu, j.nom, j.prix FROM panier p
                      JOIN jeu j ON p.id_jeu = j.id_jeu
                      WHERE p.id_utilisateur = ?");
$stmt->execute([$id_utilisateur]);
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($panier)) {
    echo json_encode(['status' => 'error', 'message' => 'Votre panier est vide.']);
    exit();
}

$stmtCredits = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmtCredits->execute([$id_utilisateur]);
$utilisateur = $stmtCredits->fetch(PDO::FETCH_ASSOC);
$credits = $utilisateur['credits'] ?? 0;

$total = 0;
foreach ($panier as $jeu) {
    $total += $jeu['prix'];
}

if ($credits < $total) {
    echo json_encode(['status' => 'error', 'message' => 'Crédits insuffisants.']);
    exit();
}

foreach ($panier as $jeu) {

    $stmtAchat = $bdd->prepare("INSERT INTO boutique (id_utilisateur, id_jeu, date_achat) VALUES (?, ?, NOW())");
    $stmtAchat->execute([$id_utilisateur, $jeu['id_jeu']]);

    $stmtSuppression = $bdd->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_jeu = ?");
    $stmtSuppression->execute([$id_utilisateur, $jeu['id_jeu']]);
}

$stmtUpdateCredits = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmtUpdateCredits->execute([$total, $id_utilisateur]);

header('Location:' . confirmation_achat);
