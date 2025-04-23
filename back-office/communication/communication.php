<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
require_once __DIR__ . '/../../path.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php
$title = 'Gestions des canaux de communications';
require('../head.php');
?>

<body class="pb-4">
    <?php
    $page = 'index.php';
    include('../navbar.php');
    ?>
</body>

</html>