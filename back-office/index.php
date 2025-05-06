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
                        Statistiques
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>