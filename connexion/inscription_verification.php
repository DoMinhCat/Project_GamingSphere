<meta charset="UTF-8">
<?php
session_start();
date_default_timezone_set('Europe/Paris');

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

    if (!isset($_POST['captcha_answer']) || !isset($_SESSION['captcha_answer'])) {
        header("Location: inscription.php?error=captcha_missing&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    $user_answer = trim(strtolower($_POST['captcha_answer']));
    $correct_answer = trim(strtolower($_SESSION['captcha_answer']));
    if ($user_answer !== $correct_answer) {
        header("Location: inscription.php?error=captcha_invalid&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    unset($_SESSION['captcha_answer']);

    if (strlen($mot_de_passe) < 8) {
        header("Location: inscription.php?error=password_length&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!preg_match('/[\W_]/', $mot_de_passe)) {
        header("Location: inscription.php?error=password_special_char&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!preg_match('/\d/', $mot_de_passe)) {
        header("Location: inscription.php?error=password_number&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!preg_match('/[A-Z]/', $mot_de_passe)) {
        header("Location: inscription.php?error=password_upper&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!ctype_digit($code_postal)) {
        header("Location: inscription.php?error=invalid_cp&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $email)) {
        header("Location: inscription.php?error=invalid_email&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom)  .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }
    if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($pseudo) || empty($ville) || empty($rue) || empty($code_postal)) {
        header("Location: inscription.php?error=empty_fields");
        exit();
    }

    $stmt = $bdd->prepare("SELECT COUNT(email) FROM utilisateurs WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $email_count = $stmt->fetchColumn();

    if ($email_count > 0) {
        header("Location: inscription.php?error=email_exists&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
        exit();
    }

    $stmt = $bdd->prepare("SELECT COUNT(pseudo) FROM utilisateurs WHERE pseudo = :pseudo");
    $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $stmt->execute();
    $pseudo_count = $stmt->fetchColumn();

    if ($pseudo_count > 0) {
        header("Location: inscription.php?error=pseudo_exists&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
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

        $verify_link = "https://gamingsphere.duckdns.org/verify_inscrire.php?token=" . $token;
        $subject = "Confirmation de votre inscription sur Gaming Sphère";
        $message = "
            <p>Bonjour <strong>$pseudo</strong>,</p>
            <p>Merci de vous être inscrit(e) sur <strong>Gaming Sphère</strong> !</p>
            <p>Pour finaliser la création de votre compte, veuillez confirmer votre adresse e-mail en cliquant sur le lien ci-dessous :</p>
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

            header('Location: inscription.php?result=success');
            exit();
        } catch (Exception $e) {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET inscrire_token = NULL, inscrire_token_expiry = NULL WHERE inscrire_token = :token");
            $stmt->execute([
                'token' => $token,
            ]);
            header('Location: inscription.php?message=' . urlencode('Erreur d\'envoi de l\'email: ') . urlencode($mail->ErrorInfo));
            exit();
        }
    } catch (PDOException $e) {
        die("Erreur lors de la création de compte : " . $e->getMessage());
    }
}
?>