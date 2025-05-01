<meta charset="UTF-8">
<?php

require '../vendor/autoload.php';
require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../path.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require('../include/database.php');

$dotenv = Dotenv::createImmutable('/var/www/PA');
$dotenv->load();

if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = strtolower(trim($_POST['email']));

    $stmt = $bdd->prepare("SELECT pseudo, reset_mdp_token, token_expiry FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $pseudo = $user['pseudo'];

        if ($user['reset_mdp_token'] && strtotime($user['token_expiry']) > time()) {
            header('Location: forgot_mdp.php?return=already_requested');
            exit();
        }

        $reset_token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $stmt = $bdd->prepare("UPDATE utilisateurs SET reset_mdp_token = :token, token_expiry = :expiry WHERE email = :email");
        $stmt->execute(['token' => $reset_token, 'expiry' => $expires, 'email' => $email]);

        $reset_link = "http://213.32.90.110/connexion/reset_mdp.php?token=" . $reset_token;
        $subject = "Demande de réinitialisation de mot de passe";
        $message = "
        <p>Bonjour <strong>$pseudo</strong>,</p>
        <p>Nous avons reçu une demande de réinitialisation de votre mot de passe.</p>
        <p><a href='$reset_link'>Cliquez ici</a> pour définir un nouveau mot de passe.</p>
        <p>Ce lien est valable pour <strong>15 minutes</strong>.</p>
        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
        <p>Cordialement,<br>L'équipe de Gaming Sphère</p>
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

            header('Location: forgot_mdp.php?return=success');
            exit();
        } catch (Exception $e) {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET reset_mdp_token = NULL, token_expiry = NULL WHERE reset_mdp_token = :token");
            $stmt->execute([
                'token' => $reset_token,
            ]);
            header('Location: forgot_mdp.php?message=Erreur d\'envoi de l\'email: ' . urlencode($mail->ErrorInfo));
            exit();
        }
    } else {
        header('Location: forgot_mdp.php?return=not_found');
        exit();
    }
}
?>