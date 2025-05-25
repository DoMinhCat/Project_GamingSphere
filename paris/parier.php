<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// Vérification des champs de base
if (
    !isset($_POST['id_tournoi'], $_POST['montant'], $_POST['cote'], $_POST['type_pari']) ||
    empty($_POST['id_tournoi']) || empty($_POST['montant']) || empty($_POST['cote']) || empty($_POST['type_pari'])
) {
    header('Location:' . paris_main . '?message=Champs manquants');
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']);
$montant = intval($_POST['montant']);
$cote = floatval($_POST['cote']);
$type_pari = $_POST['type_pari']; // 'solo' ou 'equipe'
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location:' . login . '?message=Utilisateur non connecté');
    exit();
}

// Vérifier que le tournoi est ouvert aux paris
$stmt = $bdd->prepare("SELECT pari_ouvert FROM tournoi WHERE id_tournoi = ?");
$stmt->execute([$id_tournoi]);
$tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournoi) {
    header('Location:' . paris_main . '?message=Tournoi introuvable');
    exit();
}
if (!$tournoi['pari_ouvert']) {
    header('Location:' . paris_main . '?message=Tournoi non ouvert aux paris');
    exit();
}

// S'assurer que la ligne credits existe
$stmt = $bdd->prepare("
    INSERT INTO credits (user_id, credits)
    VALUES (?, 0)
    ON DUPLICATE KEY UPDATE credits = credits
");
if (!$stmt->execute([$user_id])) {
    header('Location:' . paris_main . '?message=Erreur lors de la création de la ligne credits');
    exit();
}

// Vérifier le solde
$stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !is_numeric($user['credits'])) {
    header('Location:' . paris_main . '?message=Erreur sur le solde des crédits');
    exit();
}
if ($user['credits'] < $montant) {
    header('Location:' . paris_main . '?message=Crédits insuffisants');
    exit();
}

// Débiter le montant
$stmt = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmt->execute([$montant, $user_id]);
if ($stmt->rowCount() === 0) {
    header('Location:' . paris_main . '?message=Débit non effectué');
    exit();
}

// Traitement selon le type de pari
if ($type_pari === 'solo') {
    if (!isset($_POST['id_joueur']) || empty($_POST['id_joueur'])) {
        header('Location:' . paris_main . '?message=Joueur non spécifié');
        exit();
    }
    $id_joueur = intval($_POST['id_joueur']);

    // Vérifier que le joueur est bien inscrit
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
    $stmt->execute([$id_tournoi, $id_joueur]);
    if ($stmt->fetchColumn() == 0) {
        header('Location:' . paris_main . '?message=Joueur non inscrit à ce tournoi');
        exit();
    }

    // Enregistrement du pari solo
    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_tournoi, id_utilisateur, id_joueur, montant, cote, statut, date_pari, type_pari, gain
        ) VALUES (?, ?, ?, ?, ?, 'en attente', NOW(), ?, 0)
    ");
    $success = $stmt->execute([$id_tournoi, $user_id, $id_joueur, $montant, $cote, $type_pari]);

} elseif ($type_pari === 'equipe') {
    if (!isset($_POST['id_equipe']) || empty($_POST['id_equipe'])) {
        header('Location:' . paris_main . '?message=Équipe non spécifiée');
        exit();
    }
    $id_equipe = intval($_POST['id_equipe']);

    // Vérifier que l'équipe est bien inscrite
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM equipe_tournois WHERE id_tournoi = ? AND id_equipe = ?");
    $stmt->execute([$id_tournoi, $id_equipe]);
    if ($stmt->fetchColumn() == 0) {
        header('Location:' . paris_main . '?message=Équipe non inscrite à ce tournoi');
        exit();
    }

    // Enregistrement du pari équipe
    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_tournoi, id_utilisateur, id_equipe, montant, cote, statut, date_pari, type_pari, gain
        ) VALUES (?, ?, ?, ?, ?, 'en attente', NOW(), ?, 0)
    ");
    $success = $stmt->execute([$id_tournoi, $user_id, $id_equipe, $montant, $cote, $type_pari]);

} else {
    header('Location:' . paris_main . '?message=Type de pari invalide');
    exit();
}

// Vérifier succès
if (!$success) {
    header('Location:' . paris_main . '?message=Erreur lors de l\'enregistrement du pari');
    exit();
}

header('Location:' . paris_main . '?message=Pari enregistré avec succès');
exit();
