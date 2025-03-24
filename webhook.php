<?php
// This script will be called by the GitHub webhook

// Verify that the incoming request is from GitHub (optional, based on a secret token)
$secret = 'odissey';  // Set this to a random string (the same one you set in the GitHub webhook)
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];  // The correct header for SHA-256 signatures
$payload = file_get_contents('php://input');

// Create the hash from the payload using your secret (SHA-256)
$hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);

// Compare the signature from the request with the hash
if (!hash_equals($signature, $hash)) {
    die('Invalid signature');
}

// If valid, execute the update script
exec('/var/www/PA/update.sh');
?>

