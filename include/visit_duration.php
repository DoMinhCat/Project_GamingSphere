<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/database.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$category = $_POST['category'] ?? null;
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : null;

file_put_contents(__DIR__ . '/debug.log', json_encode([
    'category' => $_POST['category'] ?? null,
    'duration' => $_POST['duration'] ?? null
], JSON_PRETTY_PRINT));

if (!$category || $duration === null) {
    http_response_code(400);
    exit;
}


$userId = $_SESSION['user_id'];
$query = $bdd->prepare("SELECT id FROM visit_duration WHERE id_utilisateur = ? AND category = ?");
$query->execute([$userId, $category]);

if ($query->rowCount() > 0) {
    $update = $bdd->prepare("UPDATE visit_duration SET duration = duration + ? WHERE id_utilisateur = ? AND category = ?");
    $update->execute([$duration, $userId, $category]);
} else {
    $insert = $bdd->prepare("INSERT INTO visit_duration (id_utilisateur, category, duration) VALUES (?, ?, ?)");
    $insert->execute([$userId, $category, $duration]);
}
