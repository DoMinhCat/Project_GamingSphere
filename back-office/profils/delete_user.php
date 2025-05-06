<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
include('../../include/database.php');
require_once __DIR__ . '/../../path.php';
try {
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $userId = $_GET['id'];

        $stmt = $bdd->prepare("DELETE FROM utilisateurs WHERE id_utilisateurs = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: ' . profils_back . '?message=deleted');
            exit();
        } else {
            header('location:' . profils_back . '?error=user_non_exist');
            exit();
        }
    } else {
        header('location:' . profils_back . '?error=id_invalid');
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = htmlspecialchars($e->getMessage());
    header('Location:' . profils_back . '?error=bdd');
    exit();
}
