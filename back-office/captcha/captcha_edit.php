<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
require('../../include/check_timeout.php');
?>


<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modification des Captchas';
require('../../include/head.php');
?>

<body>
    <?php
    $page = index_back;
    include("../navbar.php"); ?>



</body>

</html>