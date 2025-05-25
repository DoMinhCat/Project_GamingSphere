<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

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
$type_pari = $_POST['type_pari'];
$user_id = $_SESSION['user_id'] ?? null;

// Vérifier solde utilisateur
$stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['credits'] < $montant) {
    header('Location:' . paris_main . '?message=' . urlencode('Crédits insuffisants'));
    exit();
}

// Débiter
$stmt = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmt->execute([$montant, $user_id]);

if ($stmt->rowCount() === 0) {
    header('Location:' . paris_main . '?message=' . urlencode('Débit non effectué'));
    exit();
}

// Enregistrement pari
$success = false;

if ($type_pari === 'solo') {
    if (empty($_POST['id_joueur'])) {
        header('Location:' . paris_main . '?message=' . urlencode('Joueur non spécifié'));
        exit();
    }
    $id_joueur = intval($_POST['id_joueur']);

    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_utilisateur, id_tournoi, montant, id_joueur, type_pari, statut, gain, date_pari, cote, id_equipe
        ) VALUES (?, ?, ?, ?, 'solo', 'en attente', 0, NOW(), ?, NULL)
    ");
    $success = $stmt->execute([$user_id, $id_tournoi, $montant, $id_joueur, $cote]);

} elseif ($type_pari === 'equipe') {
    if (empty($_POST['id_equipe'])) {
        header('Location:' . paris_main . '?message=' . urlencode('Équipe non spécifiée'));
        exit();
    }
    $id_equipe = intval($_POST['id_equipe']);

    $stmt = $bdd->prepare("
        INSERT INTO paris (
            id_utilisateur, id_tournoi, montant, id_equipe, type_pari, statut, gain, date_pari, cote, id_joueur
        ) VALUES (?, ?, ?, ?, 'equipe', 'en attente', 0, NOW(), ?, NULL)
    ");
    $success = $stmt->execute([$user_id, $id_tournoi, $montant, $id_equipe, $cote]);

} else {
    header('Location:' . paris_main . '?message=' . urlencode('Type de pari invalide'));
    exit();
}

if (!$success) {
    header('Location:' . paris_main . '?message=' . urlencode("Erreur lors de l'enregistrement du pari"));
    exit();
}

header('Location:' . paris_main . '?message=' . urlencode("Pari enregistré avec succès"));
exit();
