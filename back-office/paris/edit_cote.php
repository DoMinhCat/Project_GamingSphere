<?php
require('../../include/database.php');
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
session_start();
if (isset($_POST['id_tournoi'], $_POST['cote'])) {
    $id_tournoi = intval($_POST['id_tournoi']);
    $cote = floatval($_POST['cote']);
    if ($cote < 1) $cote = 1;
    $stmt = $bdd->prepare("UPDATE tournoi SET cote = ? WHERE id_tournoi = ?");
    $stmt->execute([$cote, $id_tournoi]);
}
header('Location: paris.php?message=EDIT_ME');
exit();