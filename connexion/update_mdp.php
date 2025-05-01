<meta charset="UTF-8">
<?php
session_start();
require_once __DIR__ . '/../path.php';
if (!empty($_SESSION['user_email'])) {
    header('location:' . index_front);
    exit();
}
require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;


include('../include/database.php');

$dotenv = Dotenv::createImmutable('/var/www/PA');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['token']) && !empty($_POST['new_mdp']) && !empty($_POST['confirm_mdp'])) {

        $token = $_POST['token'];

        if ($_POST['new_mdp'] !== $_POST['confirm_mdp']) {
            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Veuillez reconfirmer votre mot de passe!"));
            exit();
        }

        if (!preg_match('/[\W_]/', $_POST['new_mdp'])) {
            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Votre mot de passe ne contient pas un symbole spécial!"));
            exit();
        }

        if (!preg_match('/[A-Z]/', $_POST['new_mdp'])) {
            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Votre mot de passe ne contient pas une lettre majuscule!"));
            exit();
        }

        if (strlen($_POST['new_mdp']) < 8) {
            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Votre mot de passe doit avoir au moins 8 caractères!"));
            exit();
        }

        if (!preg_match('/\d/', $_POST['new_mdp'])) {
            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Votre mot de passe ne contient pas de chiffre!"));
            exit();
        }

        $new_password = password_hash($_POST['new_mdp'], PASSWORD_DEFAULT);

        try {
            $stmt = $bdd->prepare("SELECT pseudo, email, last3_mdp FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW() LIMIT 1");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $pseudo = $user['pseudo'];
                $email = $user['email'];
                $last_passwords = json_decode($user['last3_mdp'], true) ?? [];

                foreach ($last_passwords as $old_password) {
                    if (password_verify($_POST['new_mdp'], $old_password)) {
                        header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Vous ne pouvez pas réutiliser un ancien mot de passe!"));
                        exit();
                    }
                }

                array_unshift($last_passwords, $new_password);
                if (count($last_passwords) > 3) {
                    array_pop($last_passwords);
                }

                $stmt = $bdd->prepare("UPDATE utilisateurs SET mot_de_passe = :mdp, reset_mdp_token = NULL, token_expiry = NULL, last3_mdp = :last_mdp WHERE reset_mdp_token = :token");
                $stmt->execute([
                    'mdp' => $new_password,
                    'token' => $token,
                    'last_mdp' => json_encode($last_passwords)
                ]);

                $subject = "Confirmation de réinitialisation de votre mot de passe";
                $message = "<p>Bonjour <strong>$pseudo</strong>,</p>
                <p>Nous vous confirmons que votre mot de passe a été réinitialisé avec succès. Si vous êtes à l'origine de cette modification, aucune action supplémentaire n'est requise.</p>
                <p>Cependant, si vous n'avez pas demandé cette réinitialisation, nous vous recommandons de modifier immédiatement votre mot de passe pour sécuriser votre compte. Vous pouvez le faire en suivant ce lien :</p>
                <p><a href='http://213.32.90.110/connexion/forgot_mdp.php'>Cliquez ici pour modifier votre mot de passe</a></p>
                <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                <p>Cordialement,</p>
                <p>L'équipe de Gaming Sphère</p>";

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

                    header('Location: forgot_mdp.php?success=1');
                    exit();
                } catch (Exception $e) {
                    header('Location: forgot_mdp.php?message=' . urlencode("Erreur d'envoi de l'email de confirmation: " . $mail->ErrorInfo));
                    exit();
                }
            } else {
                header('Location: reset_mdp_err.php?message=' . urlencode("Echec de la mise à jour du mot de passe"));
                exit();
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('Location: forgot_mdp.php?message=' . urlencode("Impossible de connecter à la base de données"));
            exit();
        }
    } else {
        header('Location: forgot_mdp.php?message=' . urlencode("Veuillez remplir tous les champs demandés"));
        exit();
    }
}
?>