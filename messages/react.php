<?php
session_start();
require('../include/database.php');

if (!isset($_SESSION['user_id'], $_POST['message_id'], $_POST['emoji'])) {
    http_response_code(400);
    exit;
}

$userId = $_SESSION['user_id'];
$messageId = (int)$_POST['message_id'];
$emoji = trim($_POST['emoji']);

try {
    $stmt = $bdd->prepare("
        INSERT INTO reactions (id_message, id_utilisateur, emoji)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE emoji = VALUES(emoji)
    ");
    $stmt->execute([$messageId, $userId, $emoji]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
