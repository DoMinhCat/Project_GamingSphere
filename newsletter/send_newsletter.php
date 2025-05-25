#!/usr/bin/php
<?php
require_once('../include/database.php');
date_default_timezone_set('Europe/Paris');

require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('/var/www/PA', null, false);
$dotenv->load();
$resend_interval = 14;

$sql = "SELECT id_utilisateurs, email, pseudo, unsubscribe_token
        FROM utilisateurs
        WHERE newsletter_sub = 1
        AND last_active < NOW() - INTERVAL ? DAY
        AND (last_newsletter_sent IS NULL OR last_newsletter_sent < NOW() - INTERVAL ? DAY)";
$stmt = $bdd->prepare($sql);
$stmt->execute([$resend_interval, $resend_interval]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASS'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($_ENV['SMTP_USER'], 'Gaming Sphère');
        $mail->addAddress($user['email']);
        $mail->isHTML(true);
        $mail->Subject = "Vous nous manquez, " . htmlspecialchars($user['pseudo']);
        $mail->Body = "
    <h2>Bonjour " . htmlspecialchars($user['pseudo']) . ",</h2>
    <p>Nous avons remarqué que vous ne nous avez pas rendu visite depuis un certain temps. Voici les nouveautés :</p>
    <ul>
      <li>De nouvelles fonctionnalités que vous allez adorer</li>
      <li>Des astuces pour tirer le meilleur parti de votre compte</li>
    </ul>
    <p><a href='https://gamingsphere.duckdns.org/connexion/login'>Revenez maintenant →</a></p>
    <p style='font-size:small'>Pour vous désabonner, <a href='https://gamingsphere.duckdns.org/newsletter/unsubscribe.php?token={$user['unsubscribe_token']}'>cliquez ici</a>.</p>";

        $mail->send();
        $update = $bdd->prepare("UPDATE utilisateurs SET last_newsletter_sent = NOW() WHERE id_utilisateurs = ?");
        $update->execute([$user['id_utilisateurs']]);
    } catch (Exception $e) {
        echo "Erreur en envoyant à {$user['email']}: {$mail->ErrorInfo}<br>";
    }
}
