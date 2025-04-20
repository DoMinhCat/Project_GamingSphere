<?php
require('../../include/database.php');
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

if (!empty($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('location:profils.php?message=id_invalid');
    exit();
}

$stmt = $bdd->prepare("SELECT id_utilisateurs, email, pseudo, nom, prenom, monnaie_virtuelle, status_ENUm, date_inscription, ville, rue, code_postal, region FROM utilisateurs WHERE id_utilisateurs=?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('location:profils.php?message=user_non_exist');
    exit();
}

$exportDate = date('d/m/Y à H:i');
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            margin: 40px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        ul {
            padding: 0;
            list-style-type: none;
        }
        li {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Informations de l’utilisateur</h1>
    <ul>';
foreach ($user as $key => $value) {
    $html .= "<li><strong>" . htmlspecialchars($key) . " :</strong> " . htmlspecialchars($value) . "</li>";
}
$html .= '
    </ul>
    <div class="footer">
        Exporté le ' . $exportDate . '
    </div>
</body>
</html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream("user_info.pdf", ["Attachment" => true]);
