<?php
require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('/var/www/PA');
$dotenv->load();

require('../include/database.php');

$email = strtolower(trim($_POST['email']));
$stmt = $bdd->prepare("SELECT id, pseudo, inscrire_token, inscrire_token_expiry, last_resend_time FROM utilisateurs WHERE email = ? AND email_verifie = 0 LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    $now = time();
    $last_resend = strtotime($user['last_resend_time']);
    if ($now - $last_resend < 60 * 5) {
        header('Location: resend_verify_inscrire.php?message=' . urlencode('Veuillez attendre quelques minutes avant de renvoyer le lien.'));
        exit();
    }

    if (empty($user['inscrire_token']) || $user['inscrire_token_expiry'] < $now) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', $now + 1800);
    } else {
        $token = $user['inscrire_token'];
        $expiry = $user['inscrire_token_expiry'];
    }

    $stmt = $bdd->prepare("UPDATE utilisateurs SET inscrire_token = ?, inscrire_token_expiry = ?, last_resend_time = NOW() WHERE id = ?");
    $stmt->execute([$token, $expiry, $user['id']]);

    $verify_link = "https://gamingsphere.duckdns.org/verify_inscrire.php?token=" . $token;
    $subject = "Confirmation de votre inscription sur Gaming Sphère";
    $message = "
    <p>Bonjour <strong>{$user['pseudo']}</strong>,</p>
    <p>Merci de vous être inscrit(e) sur <strong>Gaming Sphère</strong> !</p>
    <p>Pour finaliser la création de votre compte, veuillez confirmer votre adresse e-mail en cliquant sur le lien ci-dessous :</p>
    <p><a href='{$verify_link}'>Cliquez ici</a> pour confirmer votre adresse email. Ce lien est valable pour <strong>30 minutes</strong>.</p>
    <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
    <p>À très bientôt sur Gaming Sphere !</p>
    <p>L'équipe de Gaming Sphère</p>
";
    $mail = new PHPMailer(true);
    try {
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
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $message;
        $mail->send();

        header('Location: resend_verify_inscrire.php?result=success');
        exit();
    } catch (Exception $e) {
        $stmt = $bdd->prepare("UPDATE utilisateurs SET inscrire_token = NULL, inscrire_token_expiry = NULL WHERE inscrire_token = :token");
        $stmt->execute([
            'token' => $token,
        ]);
        header('Location: resend_verify_inscrire.php?message=' . urlencode('Erreur d\'envoi de l\'email: ') . urlencode($mail->ErrorInfo) . '. Veuillez réessayer plus tard.');
        exit();
    }
} else {
    header('Location: resend_verify_inscrire.php?message=' . urlencode('Email introuvable ou déjà vérifié'));
    exit();
}
