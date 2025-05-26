<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id_utilisateur = $_SESSION['user_id'];
$email_utilisateur = $_SESSION['user_email'] ?? '';

$stmt = $bdd->prepare("
    SELECT j.nom, j.prix, b.date_achat
    FROM boutique b
    JOIN jeu j ON b.id_jeu = j.id_jeu
    WHERE b.id_utilisateur = ? AND b.date_achat >= NOW() - INTERVAL 10 MINUTE
    ORDER BY b.date_achat DESC
");
$stmt->execute([$id_utilisateur]);
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($achats) === 0) {
    echo "Aucun achat récent trouvé.";
    exit;
}
$total = 0;
$achat_html = '';
foreach ($achats as $jeu) {
    $achat_html .= '
        <tr>
            <td>' . htmlspecialchars($jeu['nom']) . '</td>
            <td>' . htmlspecialchars(number_format($jeu['prix'], 2)) . ' €</td>
            <td>' . date('d/m/Y H:i', strtotime($jeu['date_achat'])) . '</td>
        </tr>';
    $total += $jeu['prix'];
}

$date_now = date('d/m/Y à H:i');
$nom_fichier = 'Facture_GamingSphere_' . date('Ymd_His') . '.pdf';

$html = "
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Facture - Gaming Sphère</h1>
    <p><strong>Date :</strong> $date_now</p>
    <p><strong>Client :</strong> " . htmlspecialchars($email_utilisateur) . "</p>

    <table>
        <thead>
            <tr>
                <th>Jeu</th>
                <th>Prix</th>
                <th>Date d'achat</th>
            </tr>
        </thead>
        <tbody>
            $achat_html
            <tr>
                <td colspan='1' class='total'>Total</td>
                <td colspan='2' class='total'>" . number_format($total, 2) . " €</td>
            </tr>
        </tbody>
    </table>

    <p style='margin-top: 40px;'>Merci pour votre achat sur Gaming Sphère !</p>
</body>
</html>
";

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream($nom_fichier, ["Attachment" => true]);
exit;
