<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_captcha'])) {
    $id = trim($_POST['id_captcha']);
    $question = trim($_POST['question']);
    $answer = trim($_POST['answer']);
    $status = trim($_POST['statut']);
    try {
        $stmt = $bdd->prepare("UPDATE captcha SET question=?, answer=?, status=? WHERE id_captcha = ?");
        $stmt->execute([$question, $answer, $status, $id]);
        header('Location:' . captcha_back . '?message=updated');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = htmlspecialchars($e->getMessage());
        header('Location:' . captcha_back . '?error=bdd');
        exit();
    }
} else {
    header('Location: ' . captcha_back . '?error=id_invalid');
    exit();
}
