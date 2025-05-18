<?php
session_start();
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require_once('../../../include/database.php');
require_once __DIR__ . '/../../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $titre = trim($_POST['titre']) ?? '';
    $contenu = trim($_POST['contenu']) ?? '';
    $category = trim($_POST['category']) ?? '';
    $id_edit = $_POST['annonceId'];

    if (empty($titre) || empty($contenu) || empty($category)) {
        header('Location:' . annonce_edit_back . '?message=' . urlencode('Veuillez remplir tous les champs !'));
        exit();
    }

    try {
        $stmt = $bdd->prepare("UPDATE forum_sujets SET titre=?, contenu=?, categories=? WHERE id_sujet = ?");
        $stmt->execute([$titre, $contenu, $category, $id_edit]);
        header('Location:' . forum_annonce_back . '?message=updated');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . forum_annonce_back . '?error=bdd');
        exit();
    }
} else {
    header('location:' . forum_annonce_back . '?error=missing_id');
    exit();
}
