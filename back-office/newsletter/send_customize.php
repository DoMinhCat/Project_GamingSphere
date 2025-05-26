<?php
require_once  __DIR__ . '/../../include/database.php';
date_default_timezone_set('Europe/Paris');

require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require  __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('/var/www/PA', null, false);
$dotenv->load();

if (isset($_POST['message'])) {
    $message = $_POST['message'];

    $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE newsletter_sub = 1;");
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($emails) {
        foreach ($emails as $email) {
            $mail = new PHPMailer(true);
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
                $mail->addAddress($email['email']);

                $mail->isHTML(true);
                $mail->Subject = 'Newsletter de Gaming Sphère';
                $mail->Body    = nl2br(htmlspecialchars($message));

                $mail->send();
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                header('location:' . newsletter_back . '?message=send_fail');
                exit;
            }
        }
        header('location:' . newsletter_back . '?message=sent');
        exit;
    } else {
        header('location:' . newsletter_back . '?error=no_sub');
        exit;
    }
} else {
    header('loccation:' . newsletter_back . '?error=invalid');
    exit;
}
