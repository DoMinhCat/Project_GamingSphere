<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reward'])) {
        $reward = $_POST['reward'];
        $stmt = $bdd->prepare("UPDATE easter set reward=? WHERE id_easter=1;");
        $stmt->execute([$reward]);
        header('location:' . easter_back . '?message=edit_ok');
        exit;
    } else {
        header('location:' . easter_back . '?error=invalid');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location:' . easter_back . '?error=bdd');
    exit();
}
