<?php
require('../include/database.php');
require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('/var/www/PA', null, false);
$dotenv->load();


if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $bdd->prepare("SELECT id_utilisateurs, inscrire_token_expiry, pseudo, email FROM utilisateurs WHERE inscrire_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if (strtotime($user['inscrire_token_expiry']) < time()) {
            header('location: status_verify.php?result=token_invalid');
            exit();
        }
        $stmt = $bdd->prepare("UPDATE utilisateurs SET email_verifie=1, inscrire_token=NULL WHERE id_utilisateurs = ?");
        $stmt->execute([$user['id_utilisateurs']]);


        $subject = "Bienvenue sur Gaming Sphère – Email vérifié avec succès !";
        $message = "<p>Bonjour <strong>{$user['pseudo']}</strong>,</p>

    <p>Votre adresse email a bien été vérifiée <br>
        Bienvenue dans l’univers de Gaming Sphère !</p>

    <p>Vous pouvez maintenant accéder à toutes les fonctionnalités de notre plateforme :</p>
    <ul>
        <li>Participer à nos tournois</li>
        <li>Suivre l’actualité gaming</li>
        <li>Rejoindre la communauté</li>
        <li>Et profiter de nos avantages exclusifs</li>
    </ul>
    <p>
        Nous sommes ravis de vous compter parmi nous, et nous espérons que vous vivrez une expérience inoubliable au sein de notre communauté.</p>
    <p>
        Si vous avez la moindre question ou besoin d’aide, notre équipe est là pour vous accompagner !</p>
    <p>
        À très bientôt sur Gaming Sphere, <br>
        L’équipe Gaming Sphere</p>";

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
            $mail->addAddress($user['email']);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;
            $mail->send();

            header('location: status_verify.php?result=success');
            exit();
        } catch (Exception $e) {
            header('Location: status_verify.php?message=' . urlencode('Erreur d\'envoi de l\'email: ') . urlencode($mail->ErrorInfo) . '. Veuillez réessayer plus tard.');
            exit();
        }
    } else {
        header('location: status_verify.php?result=token_expire');
        exit();
    }
} else {
    header('location: status_verify.php?result=token_invalid');
    exit();
}
