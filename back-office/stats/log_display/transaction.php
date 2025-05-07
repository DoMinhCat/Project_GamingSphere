<?php
session_start();
$login_page = '/connexion/login.php';
require('../../check_session.php');
require('/include/database.php');
require('/include/check_timeout.php');
require_once __DIR__ . '/path.php';
?>
<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Log des transactions';
require('../head.php');
?>

<body>
    <?php
    $page = stats_main;
    include('../navbar.php'); ?>
    <main class="container my-5">
        <h1 class="text-center my-5">Transactions</h1>

    </main>
</body>

</html>