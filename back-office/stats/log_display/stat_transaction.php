<?php
header('Content-Type: application/json');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$lines = array_reverse(file('../../../log/log_transaction.txt'));
$pattern = '/^(\d{4})\/(\d{2})\/(\d{2}) - \d{2}:\d{2}:\d{2} - Paiement (réussi|échoué|annulé) de (.+?)(?: - (?:(\d+)\s+credits\s+ajoutés\.|en raison de : (.+)))?$/';


$nb = 0;
$rate = 0;
$revenue = 0;
$total = 0;
$cancel = 0;
foreach ($lines as $line) {
    if (preg_match($pattern, trim($line), $match)) {

        $logYear = $match[1];
        $logMonth = $match[2];
        $status = strtolower($match[4]);

        if (($month === '' || $logMonth === $month) &&
            ($year === '' || $logYear === $year)
        ) {
            $total++;
            if (str_contains($status, 'réussi')) {
                $nb++;
            } elseif (str_contains($status, 'annulé')) {
                $cancel++;
            }
            if (isset($match[6])) $revenue += $match[6];
        }
    }
}
$rate = $total > 0 ? round($nb / $total * 100, 2) . '%' : '0%';

echo json_encode([
    'nb' => $nb,
    'rate' => $rate,
    'revenue' => $revenue,
    'cancel' => $cancel
]);
