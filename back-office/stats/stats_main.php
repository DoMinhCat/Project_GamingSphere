<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestion de statistiques et logs';
require('../head.php');
?>

<body>
    <?php
    $page = index_back;
    include('../navbar.php'); ?>

    <main class="container my-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_login ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Log des connexions
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_inscription ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Log des inscriptions/modification de mot de passe
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_transaction ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Log des transactions
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= stats_duree ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Durée de la visite
                    </a>
                </div>
            </div>

        </div>
    </main>

</body>

</html>