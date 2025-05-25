<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
include('../include/database.php');
require_once __DIR__ . '/../path.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour participer.']);
    exit();
}

if (!isset($_POST['id_tournoi']) || empty($_POST['id_tournoi'])) {
    echo json_encode(['success' => false, 'message' => 'ID du tournoi manquant.']);
    exit();
}

$id_tournoi = intval($_POST['id_tournoi']);
$user_id = intval($_SESSION['user_id']);

try {
    $stmt = $bdd->prepare("SELECT type FROM tournoi WHERE id_tournoi = ?");
    $stmt->execute([$id_tournoi]);
    $tournoi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tournoi) {
        echo json_encode(['success' => false, 'message' => 'Tournoi introuvable.']);
        exit();
    }

    if ($tournoi['type'] === 'Équipe' || $tournoi['type'] === 'equipe') {
        $stmt = $bdd->prepare("
            SELECT me.id_equipe 
            FROM membres_equipe me
            WHERE me.id_utilisateur = ? AND me.role = 'capitaine'
        ");
        $stmt->execute([$user_id]);
        $id_equipe = $stmt->fetchColumn();

        if (!$id_equipe) {
            echo json_encode(['success' => false, 'message' => 'Seul le capitaine de l\'équipe peut inscrire l\'équipe à ce tournoi.']);
            exit();
        }

        $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND id_team = ?");
        $stmt->execute([$id_tournoi, $id_equipe]);
        $already_registered = $stmt->fetchColumn();

        if ($already_registered > 0) {
            echo json_encode(['success' => false, 'message' => 'Votre équipe est déjà inscrite à ce tournoi.']);
            exit();
        }

        $stmt = $bdd->prepare("INSERT INTO inscription_tournoi (id_tournoi, user_id, date_inscription, id_team) VALUES (?, ?, NOW(), ?)");
        $stmt->execute([$id_tournoi, $user_id, $id_equipe]);

        echo json_encode(['success' => true, 'message' => 'Votre équipe a été inscrite avec succès au tournoi.']);
        exit();
    } else {
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM inscription_tournoi WHERE id_tournoi = ? AND user_id = ?");
        $stmt->execute([$id_tournoi, $user_id]);
        $already_registered = $stmt->fetchColumn();

        if ($already_registered > 0) {
            echo json_encode(['success' => false, 'message' => 'Vous êtes déjà inscrit à ce tournoi.']);
            exit();
        }

        $stmt = $bdd->prepare("INSERT INTO inscription_tournoi (id_tournoi, user_id, date_inscription) VALUES (?, ?, NOW())");
        $stmt->execute([$id_tournoi, $user_id]);

        echo json_encode(['success' => true, 'message' => 'Inscription réussie.']);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription : ' . htmlspecialchars($e->getMessage())]);
    exit();
}
