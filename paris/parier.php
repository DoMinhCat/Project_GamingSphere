<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// Vérification des champs du formulaire
if (
    !isset($_POST['id_tournoi'], $_POST['choix'], $_POST['montant'], $_POST['cote'], $_POST['type_pari']) ||
    empty($_POST['id_tournoi']) || empty($_POST['choix']) || empty($_POST['montant']) || empty($_POST['cote']) || empty($_POST['type_pari'])
) {
    die('Debug: Champs manquants');
}

$id_tournoi = intval($_POST['id_tournoi']);
$choix = $_POST['choix'];
$montant = intval($_POST['montant']);
$cote = floatval($_POST['cote']);
$type_pari = $_POST['type_pari']; // 'solo' ou 'equipe'
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die('Debug: Utilisateur non connecté');
}

// Vérifier que le tournoi est ouvert aux paris
$stmt = $bdd->prepare("SELECT pari_ouvert FROM tournoi WHERE id_tournoi = ?");
$stmt->execute([$id_tournoi]);
$tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournoi) {
    die('Debug: Tournoi introuvable');
}
if (!$tournoi['pari_ouvert']) {
    die('Debug: Tournoi non ouvert aux paris');
}

// S'assurer que la ligne credits existe
$stmt = $bdd->prepare("
    INSERT INTO credits (user_id, credits)
    VALUES (?, 0)
    ON DUPLICATE KEY UPDATE credits = credits
");
if (!$stmt->execute([$user_id])) {
    die('Debug: Erreur lors de la création de la ligne credits');
}

// Vérifier le solde
$stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Debug: Ligne credits non trouvée');
}
if (!is_numeric($user['credits'])) {
    die('Debug: Valeur credits non numérique');
}
if ($user['credits'] < $montant) {
    die('Debug: Crédits insuffisants ('.$user['credits'].' < '.$montant.')');
}

// Débiter le montant
$stmt = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmt->execute([$montant, $user_id]);
if ($stmt->rowCount() === 0) {
    die('Debug: Débit non effectué');
}

// Enregistrer le pari
$stmt = $bdd->prepare("
    INSERT INTO paris (
        id_tournoi, id_utilisateur, choix, montant, cote, statut, date_pari, type_pari, gain
    ) VALUES (?, ?, ?, ?, ?, 'en attente', NOW(), ?, 0)
");
if (!$stmt->execute([$id_tournoi, $user_id, $choix, $montant, $cote, $type_pari])) {
    die('Debug: Erreur lors de l\'enregistrement du pari : ' . implode(' | ', $stmt->errorInfo()));
}

die('Debug: Pari enregistré avec succès !');