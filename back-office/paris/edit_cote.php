<?php
session_start();
require('../../include/database.php');
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';

if (isset($_POST['id_tournoi'], $_POST['id_team'], $_POST['cote'], $_POST['id_user'])) {
    $id_tournoi = intval($_POST['id_tournoi']);
    $id_team = intval($_POST['id_team']);
    $id_user = intval($_POST['id_user']);
    $cote = floatval($_POST['cote']);
    if ($cote < 1) $cote = 1;

    $stmt = $bdd->prepare("
        INSERT INTO cote_participant (id_tournoi, id_team, id_user, cote)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE cote = VALUES(cote)
    ");
    $stmt->execute([$id_tournoi, $id_team, $id_user, $cote]);
}

header('Location: paris.php?message=EDIT_ME');
exit();
