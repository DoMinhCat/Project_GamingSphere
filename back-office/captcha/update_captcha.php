<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
require('../../include/check_timeout.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_captcha'])) {
    $id = htmlspecialchars($_POST['id_captcha']);
    $question = htmlspecialchars($_POST['question']);
    $answer = htmlspecialchars($_POST['answer']);
    $status = htmlspecialchars($_POST['status']);
    try {
        $stmt = $bdd->prepare("UPDATE captcha SET question=?, answer=?, statut=? WHERE id_captcha = ?");
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
