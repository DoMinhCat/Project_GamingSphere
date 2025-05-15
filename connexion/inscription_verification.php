<meta charset="UTF-8">
<?php
session_start();
date_default_timezone_set('Europe/Paris');

require '/var/www/PA/PHPMailer/src/PHPMailer.php';
require '/var/www/PA/PHPMailer/src/SMTP.php';
require '/var/www/PA/PHPMailer/src/Exception.php';
require '../vendor/autoload.php';
require_once __DIR__ . '/../path.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('/var/www/PA', null, false);
$dotenv->load();

require_once('../include/database.php');
function writeLogInscrire(string $email, bool $success, string $error_inscript): void
{
    $stream = fopen('../log/log_inscription.txt', 'a+');
    if ($success)
        $line = date('Y/m/d - H:i:s') . ' - Création du compte réussie de ' . $email . ' - ' . $error_inscript . "\n";
    else
        $line = date('Y/m/d - H:i:s') . ' - Création du compte échouée de ' . $email . ' - en raison de : ' . $error_inscript . "\n";
    fputs($stream, $line);
    fclose($stream);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $email = strtolower($email);
    $mot_de_passe = trim($_POST['mot_de_passe']);
    $pseudo = trim($_POST['pseudo']);
    $ville = trim($_POST['ville']);
    $rue = trim($_POST['rue']);
    $region = trim($_POST['region']);
    $code_postal = trim($_POST['code_postal']);
    $date = date('Y-m-d H:i:s');
    $captcha_rep = strtolower(trim($_POST['captcha_answer']));
    $id_captcha = trim($_POST['id_captcha']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($pseudo) || empty($ville) || empty($rue) || empty($code_postal) || empty($captcha_rep)) {
        writeLogInscrire($email, false, 'informations personnelles manquantes');
        header('Location:' . inscription . '?error=empty_fields');
        exit();
    }
    if (empty($id_captcha)) {
        writeLogInscrire($email, false, 'id captcha manquant');
        header('Location:' . inscription . '?message=' . urlencode('Une erreur s\'est produite. Veuillez réesayer plus tard.'));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) {
        writeLogInscrire($email, false, 'mauvais format de l\'email');
        header('Location:' . inscription . '?error=invalid_email&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom)  .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    try {
        $stmt = $bdd->prepare("SELECT COUNT(email) FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $email_count = $stmt->fetchColumn();

        if ($email_count > 0) {
            writeLogInscrire($email, false, 'adresse email dèjà inscrite');
            header('Location:' . inscription . '?error=email_exists&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
    } catch (PDOException $e) {
        writeLogInscrire($email, false, 'Erreur de bdd : ' . $e->getMessage());
        header('Location:' . inscription . '?message=' . urlencode("Erreur lors de la création de compte"));
        exit();
    }
    try {
        $stmt = $bdd->prepare("SELECT COUNT(pseudo) FROM utilisateurs WHERE pseudo = :pseudo");
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->execute();
        $pseudo_count = $stmt->fetchColumn();

        if ($pseudo_count > 0) {
            writeLogInscrire($email, false, 'pseudo dèjà inscrit');
            header('Location:' . inscription . '?error=pseudo_exists&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
    } catch (PDOException $e) {
        writeLogInscrire($email, false, 'Erreur de bdd : ' . $e->getMessage());
        header('Location:' . inscription . '?message=' . urlencode("Erreur lors de la création de compte"));
        exit();
    }
    if (!preg_match('/[\W_]/', $mot_de_passe)) {
        writeLogInscrire($email, false, 'critères de mot de passe non remplis');
        header('Location:' . inscription . '?error=password_special_char&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }

    if (!ctype_digit($code_postal)) {
        writeLogInscrire($email, false, 'mauvais format de code postal');
        header('Location:' . inscription . '?error=invalid_cp&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!isset($captcha_rep)) {
        writeLogInscrire($email, false, 'réponse captcha manquantes');
        header('Location:' . inscription . '?error=captcha_missing&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }


    $stmt = $bdd->prepare("SELECT id_captcha,answer FROM captcha where id_captcha=? LIMIT 1;");
    $stmt->execute([$id_captcha]);
    $random_question = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$random_question) {
        writeLogInscrire($email, false, 'réponse captcha non trouvé');
        header('Location:' . inscription . '?message=' . urlencode(string: 'Une erreur s\'est produite. Veuillez réesayer plus tard.'));
        exit();
    }

    $correct_answer = trim(strtolower($random_question['answer']));
    if ($captcha_rep !== $correct_answer) {
        writeLogInscrire($email, false, 'mauvaise réponse de captcha');
        header('Location:' . inscription . '?error=captcha_invalid&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }

    if (strlen($mot_de_passe) < 8) {
        writeLogInscrire($email, false, 'critères de mot de passe non remplis');
        header('Location:' . inscription . '?error=password_length&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!preg_match('/\d/', $mot_de_passe)) {
        writeLogInscrire($email, false, 'critères de mot de passe non remplis');
        header('Location:' . inscription . 'error=password_number&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!preg_match('/[A-Z]/', $mot_de_passe)) {
        writeLogInscrire($email, false, 'critères de mot de passe non remplis');
        header('Location:' . inscription . '?error=password_upper&nom=' . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }





    $token = bin2hex(random_bytes(32));
    $expire_inscrire = date("Y-m-d H:i:s", strtotime("+30 minutes"));
    $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    try {
        $stmt = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, pseudo, date_inscription, ville, rue, code_postal, status_ENUm, inscrire_token, inscrire_token_expiry) 
                               VALUES (:nom, :prenom, :email, :mot_de_passe, :pseudo, :date_inscription, :ville, :rue, :code_postal, :status_ENUm, :token, :expire)");
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe_hache, PDO::PARAM_STR);
        $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $stmt->bindParam(':date_inscription', $date, PDO::PARAM_STR);
        $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
        $stmt->bindParam(':rue', $rue, PDO::PARAM_STR);
        $stmt->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);
        $status_enum = 'Client';
        $stmt->bindParam(':status_ENUm', $status_enum, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->bindParam(':expire', $expire_inscrire, PDO::PARAM_STR);
        $stmt->execute();

        $lastUserId = $bdd->lastInsertId();
        $insertCredits = $bdd->prepare("INSERT INTO credits (user_id, credits) VALUES (?, 0)");
        $insertCredits->execute([$lastUserId]);

        $verify_link = "https://gamingsphere.duckdns.org/connexion/verify_inscrire.php?token=" . $token;
        $subject = "Confirmation de votre inscription sur Gaming Sphère";
        $message = "
            <p>Bonjour <strong>$pseudo</strong>,</p>
            <p>Merci de vous être inscrit(e) sur <strong>Gaming Sphère</strong> !<br>
            Pour finaliser la création de votre compte, veuillez confirmer votre adresse e-mail en cliquant sur le lien ci-dessous :</p>
            <p><a href='$verify_link'>Cliquez ici</a> pour confirmer votre adresse email. Ce lien est valable pour <strong>30 minutes</strong>.</p>
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
            writeLogInscrire($email, true, 'email de vérification envoyé');
            header('Location:' . inscription . '?result=success');
            exit();
        } catch (Exception $e) {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET inscrire_token = NULL, inscrire_token_expiry = NULL WHERE inscrire_token = :token");
            $stmt->execute([
                'token' => $token,
            ]);
            writeLogInscrire($email, true, 'erreur lors de l\'envoi de l\'email de vérification : ' . $mail->ErrorInfo);
            header('Location:' . inscription . '?message=' . urlencode('Votre compte a été crée. Mais il y a une erreur lors de l\'envoi de l\'email de vérification, veuillez redemander l\'email plus tard.'));
            exit();
        }
    } catch (PDOException $e) {
        writeLogInscrire($email, false, 'Erreur de bdd : ' . $e->getMessage());
        header('Location:' . inscription . '?message=' . urlencode("Erreur lors de la création de compte"));
        exit();
    }
}
?>