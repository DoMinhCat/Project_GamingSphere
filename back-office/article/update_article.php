<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if (isset($_POST['id_article'])) {
    $titre = trim($_POST['titre']) ?? '';
    $contenu = trim($_POST['contenu']) ?? '';
    $category = !empty($_POST['category_new']) ? trim($_POST['category_new']) : trim($_POST['category_choose']);
    $id_edit = $_POST['id_article'];

    if (empty($titre) || empty($contenu) || empty($category)) {
        header('Location:' . article_edit_back . '?error=missing_fields&id=' . urlencode($id_edit));
        exit();
    }
    $imageName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $imageName = $fileName;
        }
    }

    try {
        if ($imageName) {
            $stmt = $bdd->prepare("UPDATE news SET titre=?, contenue=?, category=?, image=? WHERE id_news = ?");
            $stmt->execute([$titre, $contenu, $category, $imageName, $id_edit]);
        } else {
            $stmt = $bdd->prepare("UPDATE news SET titre=?, contenue=?, category=? WHERE id_news = ?");
            $stmt->execute([$titre, $contenu, $category, $id_edit]);
        }
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