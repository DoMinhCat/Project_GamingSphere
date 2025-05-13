<?php
session_start();
require_once(__DIR__ . '/database.php');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['category'], $data['seconds'])) {
    http_response_code(400);
    exit;
}

$userId = $_SESSION['user_id'];
$category = $data['category'];
$seconds = (int)$data['seconds'];

$query = $bdd->prepare("SELECT id FROM visit_duration WHERE id_utilisateur = ? AND category = ?");
$query->execute([$userId, $category]);

if ($query->rowCount() > 0) {
    $update = $bdd->prepare("UPDATE visit_duration SET duration = duration + ? WHERE id_utilisateur = ? AND category = ?");
    $update->execute([$seconds, $userId, $category]);
} else {
    $insert = $bdd->prepare("INSERT INTO visit_duration (id_utilisateur, category, duration) VALUES (?, ?, ?)");
    $insert->execute([$userId, $category, $seconds]);
}
