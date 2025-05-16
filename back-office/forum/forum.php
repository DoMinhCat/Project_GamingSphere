<?php
session_start();
require_once('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestion du forum';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = index_back;
    include('../navbar.php');
    ?>
    <main class="container my-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_login ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Annonces
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_inscription ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Support
                    </a>
                </div>
            </div>
            <div class="col">
                <div class="h-100">
                    <a href="<?= log_transaction ?>" class="tableau-card h-100 d-flex flex-column justify-content-center text-white text-decoration-none text-center py-4 bg-primary rounded shadow-sm">
                        Discussions
                    </a>
                </div>
            </div>

        </div>
    </main>
</body>

</html>