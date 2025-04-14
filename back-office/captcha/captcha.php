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
$title = 'Questions captcha';
require('../head.php');
?>

<body class="p-3">
    <?php
    $page = 'index.php';
    include('../navbar.php');
    ?>
</body>

</html>