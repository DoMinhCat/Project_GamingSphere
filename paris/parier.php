<?php
session_start();
require('../include/database.php');
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

// Vérification des champs du formulaire
if (
    !isset($_POST['id_tournoi'], $_POST['choix'], $_POST['montant'], $_POST['cote']) ||
    empty($_POST['id_tournoi']) || empty($_POST['choix']) || empty($_POST['montant']) || empty($_POST['cote'])
) {
    header('Location: paris_main.php?message=Veuillez remplir tous les champs.');
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']);
$choix = $_POST['choix'];
$montant = intval($_POST['montant']);
$cote = floatval($_POST['cote']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header('Location: ../connexion/login.php?message=Veuillez vous connecter.');
    exit();
}

// Vérifier que le tournoi est pariable
$stmt = $bdd->prepare("SELECT pari_ouvert FROM tournoi WHERE id_tournoi = ?");
$stmt->execute([$id_tournoi]);
$tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tournoi || !$tournoi['pari_ouvert']) {
    header('Location: paris_main.php?message=Ce tournoi n\'est pas ouvert aux paris.');
    exit();
}

// Vérifier que l'utilisateur a assez de crédits
$stmt = $bdd->prepare("SELECT credits FROM utilisateurs WHERE id_utilisateurs = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['credits'] < $montant) {
    header('Location: paris_main.php?message=Crédits insuffisants.');
    exit();
}

// Débiter les crédits de l'utilisateur
$stmt = $bdd->prepare("UPDATE utilisateurs SET credits = credits - ? WHERE id_utilisateurs = ?");
$stmt->execute([$montant, $user_id]);

// Enregistrer le pari
$stmt = $bdd->prepare("
    INSERT INTO paris (id_tournoi, id_utilisateur, choix, montant, cote, statut, date_pari)
    VALUES (?, ?, ?, ?, ?, 'en attente', NOW())
");
$stmt->execute([$id_tournoi, $user_id, $choix, $montant, $cote]);

header('Location: paris_main.php?message=Pari enregistré avec succès !');
exit();