<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tournoi'])) {
    $nom_tournoi = trim($_POST['nom_tournoi']) ?? '';
    $date_debut = trim($_POST['date_debut']) ?? '';
    $date_fin = trim($_POST['date_fin']) ?? '';
    $jeu = trim($_POST['jeu']) ?? '';
    $statut = trim($_POST['statut']) ?? '';
    $type_tournoi = trim($_POST['type_tournoi']) ?? '';
    $id_edit = $_POST['id_tournoi'];

    if (empty($nom_tournoi) || empty($date_debut) || empty($date_fin) || empty($jeu) || empty($statut) || empty($type_tournoi)) {
        echo "<div class='alert alert-danger'>Tous les champs sont obligatoires.</div>";
    }

    try {
        $stmt = $bdd->prepare("UPDATE tournoi SET nom_tournoi=?, date_debut=?, date_fin=?, status_ENUM=?, jeu=?, type=? WHERE id_tournoi = ?");
        $stmt->execute([$nom_tournoi, $date_debut, $date_fin, $statut, $jeu, $type, $id_edit]);
        header('Location:' . tournois_back . '?updated=1');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . tournois_back . '?error=db');
        exit();
    }
} else {
    header('Location:' . tournois_back . '?error=missing_id');
    exit();
}
