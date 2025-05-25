<?php
session_start();
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');

// Récupère l'ID du tournoi et le vainqueur (par exemple via POST)
if (isset($_POST['id_tournoi'], $_POST['id_gagnant'])) {
    $id_tournoi = intval($_POST['id_tournoi']);
    $id_gagnant = intval($_POST['id_gagnant']);

    // Mets à jour le tournoi avec le gagnant
    $stmt = $bdd->prepare("UPDATE tournoi SET vainqueur = ? WHERE id_tournoi = ?");
    $stmt->execute([$id_gagnant, $id_tournoi]);

    // Récupère tous les paris en attente sur ce tournoi
    $stmt = $bdd->prepare("SELECT * FROM paris WHERE id_tournoi = ? AND statut = 'en attente'");
    $stmt->execute([$id_tournoi]);
    $paris = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($paris as $pari) {
        $gagne = ($pari['id_equipe'] == $id_gagnant || $pari['id_joueur'] == $id_gagnant);
        $statut = $gagne ? 'gagné' : 'perdu';
        $gain = $gagne ? $pari['montant'] * $pari['cote'] : 0;

        // Met à jour le pari
        $stmt2 = $bdd->prepare("UPDATE paris SET statut = ?, gain = ? WHERE id_pari = ?");
        $stmt2->execute([$statut, $gain, $pari['id_pari']]);

        // Crédite le gain à l'utilisateur si gagné
        if ($gagne && $gain > 0) {
            $stmt3 = $bdd->prepare("UPDATE credits SET credits = credits + ? WHERE user_id = ?");
            $stmt3->execute([$gain, $pari['id_utilisateur']]);
        }
    }
    header('Location: paris.php?message=Résultats traités');
    exit();
}
header('Location: paris.php?message=Erreur');
exit();