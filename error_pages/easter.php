<?php
require_once __DIR__ . '/../path.php';
require('../include/database.php');
session_start();
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

$first_time = 0;
if (!empty($_SESSION['user_id'])) {
    $id_user = $_SESSION['user_id'];
    $email = $_SESSION['user_email'];
    $pseudo = $_SESSION['user_pseudo'];
    try {
        $stmt = $bdd->prepare("SELECT easter_found from utilisateurs WHERE id_utilisateurs=?;");
        $stmt->execute([$id_user]);
        $easter_status = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($easter_status['easter_found'] == 0) {
            $stmt = $bdd->prepare("UPDATE credits SET credits=credits+10 WHERE user_id=?;");
            $stmt->execute([$id_user]);

            $stmt = $bdd->prepare("UPDATE utilisateurs SET easter_found=1 WHERE id_utilisateurs=?;");
            $stmt->execute([$id_user]);
            $first_time = 1;

            $subject = "Vous avez trouvé notre Easter egg !!";
            $message = "
            <p>Bonjour et élicitations <strong>$pseudo</strong>,</p>
            <p>Nous sommes ravis de vous annoncer que vous avez trouvé <strong>notre Easter egg caché</strong> ! C'est une excellente découverte ! !<br>
            Pour vous remercier de votre perspicacité, nous avons ajouté <strong>10 crédits</strong> à votre compte. Vous pouvez les utiliser dès maintenant.</p>
            <p>Continuez à explorer, il y a peut-être d'autres surprises qui vous attendent !

            </p>
            <p>Cordialement,</p>
            <p>L'équipe de Gaming Sphère</p>";
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
            $mail->addAddress($email);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $message;
            $mail->send();
        }
    } catch (PDOException) {
        header('location:' . index_front . '?message=bdd');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Easter egg';
include('../include/head.php');
?>

<head>
    <style>
        .easter-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .celebration-icon {
            font-size: 4rem;
            color: #ffd700;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-20px);
            }

            60% {
                transform: translateY(-10px);
            }
        }

        .reward-badge {
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            color: #333;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            }

            to {
                box-shadow: 0 6px 20px rgba(255, 215, 0, 0.6);
            }
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin: 10px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .team-member {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            transition: transform 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .team-member:hover {
            transform: scale(1.05) translateY(-5px);
        }

        .team-member img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 10%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .main-content {
            position: relative;
            z-index: 2;
        }

        .title-gradient {
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #feca57);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 3s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body>
    <?php include('../include/header.php'); ?>



    <main class="container py-5 main-content">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="easter-card p-5 text-center">
                    <div class="mb-4">
                        <div class="celebration-icon">🎉</div>
                        <h1 class="title-gradient display-4 fw-bold mb-3">
                            Félicitations!
                        </h1>
                        <h2 class="text-primary mb-4">Vous avez trouvé un easter egg !!!</h2>
                    </div>

                    <?php if ($first_time == 1 && !empty($_SESSION['user_id'])): ?>
                        <div class="mb-5">
                            <div class="reward-badge d-inline-block">
                                <i class="bi bi-coin me-2"></i>
                                +10 Crédits gagnés ! 🎊
                            </div>
                            <p class="text-success mt-3 fs-5">Vous avez gagné 10 crédits en récompense. Yay !</p>
                        </div>
                    <?php endif; ?>

                    <div class="mb-5">
                        <h3 class="text-dark mb-4">
                            Bienvenue, hacker curieux !
                        </h3>
                        <p class="lead text-muted">Voici quelques stats du projet :</p>
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="stats-card text-dark">
                                <h5><i class="bi bi-trophy-fill text-warning me-2"></i>Note attendue</h5>
                                <p class="mb-0">19/20 <small>(Nous savons que c'est irréaliste )</small></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card text-dark">
                                <h5><i class="bi bi-bug-fill text-danger me-2"></i>Bugs rencontrés</h5>
                                <p class="mb-0">Trop pour les compter 🐛</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card text-dark">
                                <h5><i class="bi bi-cup-hot-fill text-brown me-2"></i>Café consommé</h5>
                                <p class="mb-0">~41 tasses ☕</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stats-card text-dark">
                                <h5><i class="bi bi-emoji-laughing-fill text-info me-2"></i>Moment drôle</h5>
                                <p class="mb-0"><em>Aucun, pourquoi pensez-vous qu'il y a eu un moment drôle ? 😅</em></p>
                            </div>
                        </div>
                    </div>

                    <!-- Team -->
                    <div class="mb-5">
                        <h2 class="text-dark mb-4">
                            <i class="bi bi-people-fill me-2"></i>
                            Notre équipe <em class="text-primary">incroyable</em>
                        </h2>
                        <div class="d-flex flex-wrap gap-4 justify-content-center">
                            <div class="team-member">
                                <img src="/error_pages/members/Paul.jpg" alt="Paul Sainctavit">
                                <div class="text-center mt-2">
                                    <small class="text-muted fw-bold">Paul</small>
                                </div>
                            </div>
                            <div class="team-member">
                                <img src="/error_pages/members/Cat.jpg" alt="Minh Cat Do">
                                <div class="text-center mt-2">
                                    <small class="text-muted fw-bold">Minh Cat</small>
                                </div>
                            </div>
                            <div class="team-member">
                                <img src="/error_pages/members/Maxime.jpg" alt="Maxime Oliveira">
                                <div class="text-center mt-2">
                                    <small class="text-muted fw-bold">Maxime</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-5">
                        <a href="<?= index_front ?>" class="btn btn-primary btn-lg px-4 py-2">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php include('../include/footer.php'); ?>
</body>

</html>