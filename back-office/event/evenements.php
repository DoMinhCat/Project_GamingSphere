<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Gestion des évènements';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = 'index.php';
    include('../navbar.php');
    ?>
</body>

</html>