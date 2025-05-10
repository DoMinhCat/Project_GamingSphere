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
$title = 'Durée visite';
require('../head.php');
?>

<body>
    <?php
    $page = stats_main;
    include('../navbar.php'); ?>

    <main class="container my-5">
        <h1 class="my-5">Durée de la visite sur le site</h1>


    </main>
</body>

</html>