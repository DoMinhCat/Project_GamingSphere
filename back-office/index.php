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
                <a href="<?= '..' . index_front ?>" class="btn btn-primary">Front Office</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="h-100">
                    <a href="<?= event_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des évènements
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= tournois_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des tournois
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= paris_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des paris
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= forum_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion du forum
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= article_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des articles
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= jeux_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des jeux
                    </a>
                </div>
            </div>

            <div class="col">
                <div class="h-100">
                    <a href="<?= profils_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des profils
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= captcha_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des captchas
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= communication_back ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Gestion des canaux de communication
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= stats_main ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Statistiques et logs
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>

</html>