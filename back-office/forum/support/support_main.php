<?php
session_start();
require_once('../../../include/database.php');
$login_page = '../../../connexion/login.php';
require('../../check_session.php');
require('../../../include/check_timeout.php');
require_once __DIR__ . '/../../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Gestion des supports';
require('../../head.php');
?>

<body class="pb-4">
    <?php
    $page = forum_back;
    include('../../navbar.php');
    ?>
    <main class="container my-5">


    </main>
</body>

</html>