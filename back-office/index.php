<?php
session_start();
$login_page = '../connexion/login.php';
require('check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Acceuil';
require('head.php');
?>

<body class="pb-4">
    <nav class="navbar navbar-dark bg-dark px-3">
        <div class="container  d-flex flex-row justify-content-between">
            <div class="d-flex align-items-center">
                <a href="#">
                    <img src="../include/LOGO ENTIER 40px.png" alt="Logo" height="30" class="rounded-circle">
                </a>
            </div>
            <div class="mx-auto text-center">
                <span class="navbar-brand">Back Office</span>
            </div>
            <div class="ms-auto">
                <a href="<?= '../' . index_back ?>" class="btn btn-primary">Front Office</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <a href="event/evenements.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des évènements
                </a>
            </div>
            <div class="col">
                <a href="tournois/tournois_main.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des tournois
                </a>
            </div>
            <div class="col">
                <a href="paris/paris.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des paris
                </a>
            </div>

            <div class="col">
                <a href="forum/forum.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion du forum
                </a>
            </div>
            <div class="col">
                <a href="article/articles.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des articles
                </a>
            </div>
            <div class="col">
                <a href="jeux/jeux.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des jeux
                </a>
            </div>

            <div class="col">
                <a href="profils/profils.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des profils
                </a>
            </div>
            <div class="col">
                <a href="captcha/captcha.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des captchas
                </a>
            </div>
            <div class="col">
                <a href="communication/communication.php" class="tableau-card d-block text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                    Gestion des canaux de communication
                </a>
            </div>

        </div>
    </div>
</body>

</html>