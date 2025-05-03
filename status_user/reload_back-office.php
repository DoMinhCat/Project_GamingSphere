<?php
require_once('..//include/database.php');

$stmt = $bdd->prepare("SELECT pseudo FROM utilisateurs WHERE last_active >= NOW() - INTERVAL 2 MINUTE");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($users);
