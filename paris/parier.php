<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// Vérification des champs
if (
    !isset($_POST['id_tournoi'], $_POST['montant'], $_POST['cote'], $_POST['type_pari']) ||
    empty($_POST['id_tournoi']) || empty($_POST['montant']) || empty($_POST['cote']) || empty($_POST['type_pari'])
) {
    header('Location:' . paris_main . '?message=' . urlencode('Champs manquants'));
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']);
$montant = intval($_POST['montant']);
$cote = floatval($_POST['cote']);
$type_pari = $_POST['type_pari']; // 'solo' ou 'equipe'
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location:' . paris_main . '?message=' . urlencode('Utilisateur non connecté'));
    exit();
}

// Vérifier le solde
$stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !is_numeric($user['credits'])) {
    header('Location:' . paris_main . '?message=' . urlencode('Erreur sur le solde des crédits'));
    exit();
}

if ($user['credits'] < $montant) {
    header('Location:' . paris_main . '?message=' . urlencode('Crédits insuffisants'));
    exit();
}

// Débiter le montant
$stmt = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmt->execute([$montant, $user_id]);
if ($stmt->rowCount() === 0) {
    header('Location:' . paris_main . '?message=' . urlencode('Débit non effectué'));
    exit();
}

$success = false;

if ($type_pari === 'solo') {
    if (!isset($_POST['id_joueur']) || empty($_POST['id_joueur'])) {
        header('Location:' . paris_main . '?message=' . urlencode('Joueur non spécifié'));
        exit();
    }
    $id_joueur = intval($_POST['id_joueur']);

    // Vérifier que le joueur est inscrit
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
    $stmt->execute([$id_tournoi, $id_joueur]);
    if ($stmt->fetchColumn() == 0) {
        header('Location:' . paris_main . '?message=' . urlencode('Joueur non inscrit à ce tournoi'));
        exit();
    }

    // Enregistrement du pari solo
    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_utilisateur, id_tournoi, montant, id_joueur, type_pari, statut, gain, date_pari, cote, id_equipe
        ) VALUES (?, ?, ?, ?, ?, 'en attente', 0, NOW(), ?, NULL)
    ");
    $success = $stmt->execute([$user_id, $id_tournoi, $montant, $id_joueur, $type_pari, $cote]);

} elseif ($type_pari === 'equipe') {
    if (!isset($_POST['id_equipe']) || empty($_POST['id_equipe'])) {
        header('Location:' . paris_main . '?message=' . urlencode('Équipe non spécifiée'));
        exit();
    }
    $id_equipe = intval($_POST['id_equipe']);

    // Vérifier que l'équipe est inscrite
    $stmt = $bdd->prepare("SELECT COUNT(*) FROM equipe_tournois WHERE id_tournoi = ? AND id_equipe = ?");
    $stmt->execute([$id_tournoi, $id_equipe]);
    if ($stmt->fetchColumn() == 0) {
        header('Location:' . paris_main . '?message=' . urlencode('Équipe non inscrite à ce tournoi'));
        exit();
    }

    // Enregistrement du pari équipe
    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_utilisateur, id_tournoi, montant, id_equipe, type_pari, statut, gain, date_pari, cote, id_joueur
        ) VALUES (?, ?, ?, ?, ?, 'en attente', 0, NOW(), ?, NULL)
    ");
    $success = $stmt->execute([$user_id, $id_tournoi, $montant, $id_equipe, $type_pari, $cote]);

} else {
    header('Location:' . paris_main . '?message=' . urlencode('Type de pari invalide'));
    exit();
}

if (!$success) {
    header('Location:' . paris_main . '?message=' . urlencode('Erreur lors de l\'enregistrement du pari'));
    exit();
}

header('Location:' . paris_main . '?message=' . urlencode('Pari enregistré avec succès'));
exit();
