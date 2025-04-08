<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Gestions des paris';
require('include/head.php');
?>

<body>
<?php
    $page='index.php';
    include('../navbar.php');
    ?>
</body>

</html>