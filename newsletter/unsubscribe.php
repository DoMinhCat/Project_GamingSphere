<?php
require_once('../include/database.php');
require_once __DIR__ . '/../path.php';
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header('location:' . index_front . '?error=' . urlencode('Lien de désabonnement invalide.'));
    exit;
}
$token = $_GET['token'];
try {
    $stmt = $bdd->prepare("UPDATE utilisateurs SET newsletter_sub = 0, unsubscribe_token = NULL WHERE unsubscribe_token = ?");
    $stmt->execute([$token]);
    if ($stmt->rowCount() > 0) {
        header('location:' . index_front . '?newsletter=' . urlencode('Vous vous êtes désabonné(e) de notre newsletter !'));
    } else {
        header('location:' . index_front . '?error=' . urlencode('Lien invalide ou déjà utilisé.'));
    }
    exit;
} catch (PDOException) {
    header('location:' . index_front . '?message=bdd');
    exit;
}
