<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
include('../../include/database.php');

$login_page = '../../connexion/login.php';
require('../check_session.php');

if (!isset($_GET['id_tournoi']) || empty($_GET['id_tournoi'])) {
    header('Location: ' . tournois_back . '?message=missing_id');
    exit();
}

$id_tournoi = intval($_GET['id_tournoi']);

try {
    $stmt = $bdd->prepare("DELETE FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);

    header('Location: ' . profils_edit_back . '?message=tournoi_deleted');
    exit();
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur lors de la suppression du tournoi : " . htmlspecialchars($e->getMessage()) . "</div>";
}
