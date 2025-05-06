<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
if (isset($_GET['id'])) {
    try {
        $id = intval($_GET['id']);
        $query = $bdd->prepare("SELECT image FROM jeu WHERE id_jeu = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . jeux_back . '?error=bdd');
        exit();
    }

    if ($result && !empty($result['image'])) {
        $imagePath = __DIR__ . "/../uploads/" . $result['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $query = $bdd->prepare("DELETE FROM jeu WHERE id_jeu = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        header('Location:' . jeux_back . '?message=deleted');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . jeux_back . '?error=bdd');
        exit();
    }
} else {
    header('Location: ' . jeux_back . '?error=id_invalid');
    exit();
}
