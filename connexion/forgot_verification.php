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
function writeLogDemandResetMdp(string $email, bool $success, string $return): void
{
    $stream = fopen('../log/log_inscription.txt', 'a+');
    if ($success)
        $line = date('Y/m/d - H:i:s') . ' - Demande de initialisation de mot de passe réussie de ' . $email . ' - ' . $return . "\n";
    else
        $line = date('Y/m/d - H:i:s') . ' - Demande de initialisation de mot de passe échouée de ' . $email . ' - en raison de : ' . $return . "\n";
    fputs($stream, $line);
    fclose($stream);
}
if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = strtolower(trim($_POST['email']));

    $stmt = $bdd->prepare("SELECT pseudo, reset_mdp_token, token_expiry FROM utilisateurs WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $pseudo = $user['pseudo'];

        if ($user['reset_mdp_token'] && strtotime($user['token_expiry']) > time()) {
            writeLogDemandResetMdp($email, false,  "demande déjà éffectuée");
            header('Location' . forgot_mdp . '?return=already_requested');
            exit();
        }

        $reset_token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        try {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET reset_mdp_token = :token, token_expiry = :expiry WHERE email = :email");
            $stmt->execute(['token' => $reset_token, 'expiry' => $expires, 'email' => $email]);

            $reset_link = 'http://213.32.90.110/connexion/' . reset_mdp . '?token=' . $reset_token;
            $subject = "Demande de réinitialisation de mot de passe";
            $message = "
        <p>Bonjour <strong>$pseudo</strong>,</p>
        <p>Nous avons reçu une demande de réinitialisation de votre mot de passe.</p>
        <p><a href='$reset_link'>Cliquez ici</a> pour définir un nouveau mot de passe.</p>
        <p>Ce lien est valable pour <strong>15 minutes</strong>.</p>
        <p>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
        <p>Cordialement,<br>L'équipe de Gaming Sphère</p>
        ";
        } catch (Exception $e) {
            writeLogDemandResetMdp($email, false,  "erreur de la bdd : " . $e->getMessage());
            header('Location:' . forgot_mdp . '?message=Erreur de la base de donnée');
            exit();
        }
    }

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
        writeLogDemandResetMdp($email, true,  "email envoyé");
        header('Location: ' . forgot_mdp . '?return=success');
        exit();
    } catch (Exception $e) {
        $stmt = $bdd->prepare("UPDATE utilisateurs SET reset_mdp_token = NULL, token_expiry = NULL WHERE reset_mdp_token = :token");
        $stmt->execute([
            'token' => $reset_token,
        ]);
        writeLogDemandResetMdp($email, false,  "erreur de l'envoi de l'email : " . $mail->ErrorInfo);
        header('Location:' . forgot_mdp . '?message=Erreur d\'envoi de l\'email: ' . urlencode($mail->ErrorInfo));
        exit();
    }
} else {
    writeLogDemandResetMdp("visiteur", false,  "l'adresse de l'email non existe");
    header('Location:' . forgot_mdp . '?return=not_found');
    exit();
}

?>