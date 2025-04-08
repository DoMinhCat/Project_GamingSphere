<?php

$secret = 'odissey';  
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];  
$payload = file_get_contents('php://input');

$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (!hash_equals($signature, $hash)) {
    die('Invalid signature');
}

exec('/var/www/PA/update.sh');
?>

