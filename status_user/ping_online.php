<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require('../include/database.php');
if (!empty($_SESSION['user_email'])) {
    $stmt = $bdd->prepare("UPDATE utilisateurs SET last_active = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}
