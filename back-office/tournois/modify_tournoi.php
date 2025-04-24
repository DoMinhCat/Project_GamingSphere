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
$title = 'Modification de tournois';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = tournois_back;
    include('../navbar.php');
    ?>
</body>

</html>