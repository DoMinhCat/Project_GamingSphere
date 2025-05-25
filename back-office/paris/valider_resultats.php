<?php
session_start();
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');

if (isset($_POST['id_tournoi'], $_POST['id_gagnant'])) {
    $id_tournoi = intval($_POST['id_tournoi']);
    $id_gagnant = intval($_POST['id_gagnant']);

    // Récupère le type du tournoi
    $stmt = $bdd->prepare("SELECT type FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);
    $type = $stmt->fetchColumn();
    $is_team = ($type === 'equipe');

    // Enregistre ou met à jour le résultat dans tournament_results (position 1)
    if ($is_team) {
        $stmt = $bdd->prepare("
            INSERT INTO tournament_results (tournament_id, team_id, position, credits_awarded)
            VALUES (?, ?, 1, 0)
            ON DUPLICATE KEY UPDATE team_id = VALUES(team_id), credits_awarded = 0
        ");
        $stmt->execute([$id_tournoi, $id_gagnant]);
    } else {
        $stmt = $bdd->prepare("
            INSERT INTO tournament_results (tournament_id, user_id, position, credits_awarded)
            VALUES (?, ?, 1, 0)
            ON DUPLICATE KEY UPDATE user_id = VALUES(user_id), credits_awarded = 0
        ");
        $stmt->execute([$id_tournoi, $id_gagnant]);
    }

    // Récupère les paris en attente
    $stmt = $bdd->prepare("SELECT * FROM paris WHERE id_tournoi = ? AND statut = 'en attente'");
    $stmt->execute([$id_tournoi]);
    $paris = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($paris as $pari) {
        $gagne = false;

        if ($is_team) {
            $gagne = ($pari['id_equipe'] == $id_gagnant);
        } else {
            $gagne = ($pari['id_joueur'] == $id_gagnant);
        }

        $statut = $gagne ? 'gagné' : 'perdu';
        $gain = $gagne ? $pari['montant'] * $pari['cote'] : 0;

        // Mise à jour du pari
        $stmt2 = $bdd->prepare("UPDATE paris SET statut = ?, gain = ? WHERE id_pari = ?");
        $stmt2->execute([$statut, $gain, $pari['id_pari']]);

        // Créditer l’utilisateur si gagné
        if ($gagne && $gain > 0) {
            $stmt3 = $bdd->prepare("
                INSERT INTO credits (user_id, credits)
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE credits = credits + VALUES(credits)
            ");
            $stmt3->execute([$pari['id_utilisateur'], $gain]);
        }
    }

    header('Location: paris.php?message=Résultats traités');
    exit();
}

header('Location: paris.php?message=Erreur');
exit();
