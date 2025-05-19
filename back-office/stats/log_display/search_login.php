<?php
require_once __DIR__ . '/../../../path.php';
if ($_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest' || !isset($_GET['search'])) {
    http_response_code(403);
    exit('Accès non-autorisé');
}
$lines = array_reverse(file('../../../log/log_login.txt'));
$pattern = '/^(\d{4}\/\d{2}\/\d{2}) - (\d{2}:\d{2}:\d{2}) - (.+?) (réussie|échouée) de (.+?)$/';
$results = [];

$search = trim($_GET['search'] ?? '');
$statusFilter = strtolower(trim($_GET['status'] ?? ''));




foreach ($lines as $line) {
    if (preg_match($pattern, trim($line), $match)) {
        $dateTime = $match[1] . ' - ' . $match[2];
        $action = $match[3];
        $status = strtolower($match[4]);
        $email = strtolower($match[5]);
        if (
            ($search === '' || str_contains($email, $search)) &&
            ($statusFilter === '' || $status === $statusFilter)
        ) {
            $results[] = [
                'datetime' => $dateTime,
                'action' => $action,
                'status' => ucfirst($status),
                'email' => $email,
            ];
        }
    }
}

if (!empty($results)) {
    foreach ($results as $result) {
        echo '<tr>';
        echo "<td class=\"align-middle\">" . htmlspecialchars($result['datetime']) . "</td>";
        echo "<td class=\"align-middle\">" .  htmlspecialchars($result['action'])  . "</td>";
        echo "<td class=\"align-middle\">" .  htmlspecialchars($result['email'])  . "</td>";
        echo "<td class=\"align-middle\">" .  htmlspecialchars($result['status'])  . "</td>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='12' class=\"text-center\">Aucun log trouvé.</td></tr>";
}
