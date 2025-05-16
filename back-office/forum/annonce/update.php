<?php
session_start();
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require_once('../../../include/database.php');
require_once __DIR__ . '/../../../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
} else {
    header('location:' . forum_annonce_back . '?error=missing_id');
    exit();
}
