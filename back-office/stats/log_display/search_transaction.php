<?php
require_once __DIR__ . '/../../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
$lines = file('../../../log/log_transaction.txt');
$pattern = '/^(\d{4}\/\d{2}\/\d{2}) - (\d{2}:\d{2}:\d{2}) - (.+?) (réussi|échoué|annulé) de (.+?)(?: - (?:en raison de : )?(.+))?$/';
$results = [];

$search = trim($_GET['search'] ?? '');
$statusFilter = strtolower(trim($_GET['status'] ?? ''));




foreach ($lines as $line) {
    if (preg_match($pattern, trim($line), $match)) {
        $dateTime = $match[1] . ' - ' . $match[2];
        $action = $match[3];
        $status = strtolower($match[4]);
        $email = strtolower($match[5]);
        $note = '';
        if ($match[6]) $note = ucfirst($match[6]);
        if (
            ($search === '' || str_contains($email, $search)) &&
            ($statusFilter === '' || $status === $statusFilter)
        ) {
            $results[] = [
                'datetime' => $dateTime,
                'action' => $action,
                'status' => ucfirst($status),
                'email' => $email,
                'note' => $note,
            ];
        }
    }
}

if (!empty($results)) {
    foreach ($results as $result) {
        echo '<tr>';
        echo "<td class=\"align-middle\">" . $result['datetime'] . "</td>";
        echo "<td class=\"align-middle\">" . $result['action'] . "</td>";
        echo "<td class=\"align-middle\">" . $result['email'] . "</td>";
        echo "<td class=\"align-middle\">" . $result['status'] . "</td>";
        echo "<td class=\"align-middle\">" . $result['note'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12' class=\"text-center\">Aucun log trouvé.</td></tr>";
}
