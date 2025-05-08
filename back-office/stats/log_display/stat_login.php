<?php
header('Content-Type: application/json');

if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$lines = file('../../../log/log_login.txt');
$pattern = '/^(\d{4})\/(\d{2})\/(\d{2}) - \d{2}:\d{2}:\d{2} - (.+?) (réussie|échouée) de (.+?)(?: - (?:en raison de : )?(.+))?$/';

$nb = 0;
$rate = 0;
$max = 0;
$dayMax = 0;
$total = 0;
$count = 1;
$dayTemp = 0;
$check = 0;
foreach ($lines as $line) {
    if (preg_match($pattern, trim($line), $match)) {

        $logYear = $match[1];
        $logMonth = $match[2];
        $status = strtolower($match[5]);
        $logDay = $match[3];

        if (($month === '' || $logMonth === $month) &&
            ($year === '' || $logYear === $year)
        ) {
            $total++;
            if (str_contains($status, 'réussie')) {
                $nb++;
            }
            if ($check == 0 || $dayTemp == $logDay) {
                $check = 1;
                $count++;
                if ($max < $count) {
                    $max = $count;
                    $dayMax = $logDay;
                }
                $dayTemp = $logDay;
            } else {
                $count = 0;
                $dayTemp = $logDay;
            }
        }
    }
}
$rate = $total > 0 ? round($nb / $total * 100, 2) . '%' : '0%';

echo json_encode([
    'nb' => $nb,
    'rate' => $rate,
    'max' => $dayMax
]);
