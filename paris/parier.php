<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

if (
    !isset($_POST['id_tournoi'], $_POST['choix'], $_POST['montant']) ||
    empty($_POST['id_tournoi']) || empty($_POST['choix']) || empty($_POST['montant'])
) {
    header('Location: paris_main.php?message=Veuillez remplir tous les champs.');
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']);
$choix = $_POST['choix'];
$montant = intval($_POST['montant']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: ../connexion/login.php?message=Veuillez vous connecter.');
    exit();
}

if ($montant <= 0) {
    header('Location: paris_main.php?message=Montant de pari invalide.');
    exit();
}

// Récupérer les infos du tournoi (pari_ouvert, type, cote)
$stmt = $bdd->prepare("SELECT pari_ouvert, type, cote FROM tournoi WHERE id_tournoi = ?");
$stmt->execute([$id_tournoi]);
$tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournoi || !$tournoi['pari_ouvert']) {
    header('Location: paris_main.php?message=Ce tournoi n\'est pas ouvert aux paris.');
    exit();
}

// Vérifier que l'utilisateur a assez de crédits
$stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['credits'] < $montant) {
    header('Location: paris_main.php?message=Crédits insuffisants.');
    exit();
}

// Débiter les crédits de l'utilisateur
$stmt = $bdd->prepare("UPDATE credits SET credits = credits - ? WHERE user_id = ?");
$stmt->execute([$montant, $user_id]);

// Préparer les valeurs pour l'INSERT
$id_equipe = null;
$id_joueur = null;
if ($tournoi['type'] === 'equipe') {
    $id_equipe = $choix;
} else {
    $id_joueur = $choix;
}

// Enregistrer le pari (on utilise la cote du tournoi pour éviter la triche)
$stmt = $bdd->prepare("
    INSERT INTO paris (id_tournoi, id_utilisateur, id_equipe, id_joueur, montant, cote, statut, date_pari)
    VALUES (?, ?, ?, ?, ?, ?, 'en attente', NOW())
");
$stmt->execute([$id_tournoi, $user_id, $id_equipe, $id_joueur, $montant, $tournoi['cote']]);

header('Location: paris_main.php?message=Pari enregistré avec succès !');
exit();