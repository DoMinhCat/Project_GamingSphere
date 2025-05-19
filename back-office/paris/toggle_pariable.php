<?php
session_start();
require('../../include/database.php');
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
if (isset($_POST['id_tournoi'], $_POST['pari_ouvert'])) {
    $id_tournoi = intval($_POST['id_tournoi']);
    $pari_ouvert = intval($_POST['pari_ouvert']) ? 1 : 0;
    $stmt = $bdd->prepare("UPDATE tournoi SET pari_ouvert = ? WHERE id_tournoi = ?");
    $stmt->execute([$pari_ouvert, $id_tournoi]);
}

header('Location: paris.php');
exit();