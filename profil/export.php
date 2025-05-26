<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/database.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$id = $_SESSION['user_id'];


$stmt = $bdd->prepare("SELECT id_utilisateurs, email, pseudo, nom, prenom, monnaie_virtuelle, status_ENUm, date_inscription, ville, rue, code_postal, region FROM utilisateurs WHERE id_utilisateurs=?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    header('location:' . index_front . '?error=' . urlencode('L\'utilisateur n\'existe pas !'));
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
        img{
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <img src="../../include/LOGO ENTIER 40px.png" alt="Logo Gaming Sphère">
    <h1>Informations de l’utilisateur</h1>
    <ul>';
foreach ($user as $key => $value) {
    switch ($key) {
        case 'id_utilisateurs':
            $label = 'User ID';
            break;
        case 'email':
            $label = 'Email';
            break;
        case 'pseudo':
            $label = 'Pseudo';
            break;
        case 'nom':
            $label = 'Nom';
            break;
        case 'prenom':
            $label = 'Prénom';
            break;
        case 'monnaie_virtuelle':
            $label = 'Somme de monnaie virtuelle';
            break;
        case 'status_ENUm':
            $label = 'Rôle';
            break;
        case 'date_inscription':
            $label = 'Date d\'inscription';
            break;
        case 'ville':
            $label = 'Ville';
            break;
        case 'rue':
            $label = 'Rue';
            break;
        case 'code_postal':
            $label = 'Code postal';
            break;
        case 'region':
            $label = 'Région';
            break;
        default:
            $label = ucfirst($key);
    }

    $html .= "<li><strong>$label :</strong> " . htmlspecialchars($value) . "</li>";
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

$dompdf->stream('user_info_id' . $id . '.pdf', ["Attachment" => true]);
