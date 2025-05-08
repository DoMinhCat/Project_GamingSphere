<?php
header('Content-Type: application/json');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$lines = file('../../../log/log_inscription.txt');
$pattern = '/^(\d{4})\/(\d{2})\/\d{2} - \d{2}:\d{2}:\d{2} - (.+?) (réussie|échouée) de (.+?)(?: - (?:en raison de : )?(.+))?$/';

$new = 0;
$deleted = 0;

foreach ($lines as $line) {
    if (preg_match($pattern, trim($line), $match)) {
        $logYear = $match[1];
        $logMonth = $match[2];
        $action = strtolower($match[3]);
        $status = $match[4];
        if (($month === '' || $logMonth === $month) &&
            ($year === '' || $logYear === $year)
        ) {

            if (str_contains($action, 'Création') && $status == 'réussie') {
                $new++;
            } elseif (str_contains($action, 'Suppression')) {
                $deleted++;
            }
        }
    }
}

echo json_encode([
    'new' => $new,
    'deleted' => $deleted
]);
