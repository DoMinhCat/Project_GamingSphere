<?php
session_start();
require('../../include/database.php');
$login_page = '../../connexion/login.php';
require('../check_session.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gestion du forum</title>

    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../../include/check_timeout.js"></script>';
    }
    ?>
</head>

<body>

</body>

</html>