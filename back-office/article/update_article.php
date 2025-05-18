<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if (isset($_POST['id_article'])) {
    $titre = trim($_POST['titre']) ?? '';
    $contenu = trim($_POST['contenu']) ?? '';
    $category = trim($_POST['category_choose']) ?? trim($_POST['category_new']);
    $id_edit = $_POST['id_article'];

    if (empty($titre) || empty($contenu) || empty($category)) {
        header('Location:' . article_edit_back . '?error=missing_fields');
        exit();
    }

    try {
        $stmt = $bdd->prepare("UPDATE news SET titre=?, contenue=?, category=? WHERE id_news = ?");
        $stmt->execute([$titre, $contenu, $category, $id_edit]);
        header('Location:' . article_back . '?message=update_success');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . article_back . '?error=bdd');
        exit();
    }
} else {
    header('Location:' . article_back . '?error=missing_id');
    exit();
}
