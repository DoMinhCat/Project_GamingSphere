<?php
session_start();
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require('../../include/database.php');
require_once __DIR__ . '/../../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Modification des Articles';
require('../head.php'); ?>

<body>
    <?php
    $page = article_back;
    include("../navbar.php"); ?>
</body>

</html>