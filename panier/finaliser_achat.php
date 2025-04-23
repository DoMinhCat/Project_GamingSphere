<?php
session_start();
require('../include/check_timeout.php');
require('../include/database.php');
require('../include/check_session.php');

$id_utilisateur = $_SESSION['user_id'];

// Récupère le panier avec les infos des jeux (dont prix)
$stmt = $bdd->prepare("SELECT p.id_jeu, j.nom, j.prix FROM panier p
                      JOIN jeu j ON p.id_jeu = j.id_jeu
                      WHERE p.id_utilisateur = ?");
$stmt->execute([$id_utilisateur]);
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifie si le panier est vide
if (empty($panier)) {
    echo json_encode(['status' => 'error', 'message' => 'Votre panier est vide.']);
    exit();
}

// Récupère les crédits de l'utilisateur
$stmtCredits = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmtCredits->execute([$id_utilisateur]);
$utilisateur = $stmtCredits->fetch(PDO::FETCH_ASSOC);
$credits = $utilisateur['credits'] ?? 0;

// Calcul du total
$total = 0;
foreach ($panier as $jeu) {
    $total += $jeu['prix'];
}

// Vérifie si l'utilisateur a assez de crédits
if ($credits < $total) {
    echo json_encode(['status' => 'error', 'message' => 'Crédits insuffisants.']);
    exit();
}

// Procède à l'achat
foreach ($panier as $jeu) {
    // Ajoute l'achat
    $stmtAchat = $bdd->prepare("INSERT INTO boutique (id_utilisateur, id_jeu, date_achat) VALUES (?, ?, NOW())");
    $stmtAchat->execute([$id_utilisateur, $jeu['id_jeu']]);

    // Supprime du panier
    $stmtSuppression = $bdd->prepare("DELETE FROM panier WHERE id_utilisateur = ? AND id_jeu = ?");
    $stmtSuppression->execute([$id_utilisateur, $jeu['id_jeu']]);
}

// Met à jour les crédits
$stmtUpdateCredits = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmtUpdateCredits->execute([$total, $id_utilisateur]);

echo json_encode(['status' => 'success', 'message' => 'Achat effectué avec succès !']);
?>
