<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
try {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['interval'])) {
        $interval = $_POST['interval'];
        $stmt = $bdd->prepare("UPDATE newsletter_interval set gap=? WHERE id_interval=1;");
        $stmt->execute([$interval]);
        header('location:' . newsletter_back . '?message=edit_ok');
        exit;
    } else {
        header('location:' . newsletter_back . '?error=invalid');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location:' . newsletter_back . '?error=bdd');
    exit();
}
