<?php

require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('../include/database.php');

if (isset($_POST['email']) && !empty($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $reset_token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $stmt = $pdo->prepare("UPDATE utilisateurs SET reset_mdp_token = :token, token_expiry = :expiry WHERE email = :email");
        $stmt->execute(['token' => $reset_token, 'expiry' => $expires, 'email' => $email]);

        $reset_link = "http://213.32.90.110/connexion/forgot_mdp.php?token=" . $reset_token;
        $subject = "Demande de réinitialisation de mot de passe";
        $message = "Veuillez suivre le lien ci-dessous afin de réinitialiser votre mot de passe:\n\n" . $reset_link;



        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mdo1@myges.fr';
            $mail->Password = 'THuog05#789';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('mdo1@myges.fr', 'Minh Cat');
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->send();
            header('Location: forgot_mdp.php?message=Email envoyé avec succès');
            exit();
        } catch (Exception $e) {
            header('Location: forgot_mdp.php?message=Erreur d\'envoi de l\'email: ' . urlencode($mail->ErrorInfo));
            exit();
        }
    } else {
        header('Location: forgot_mdp.php?message=Adresse email non trouvée');
        exit();
    }
}


