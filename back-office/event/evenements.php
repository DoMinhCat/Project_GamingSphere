<?php
require('../../include/database.php');
require('../../include/check_timeout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gestion des évènements</title>

    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../../include/check_timeout.js"></script>';
    }
    ?>
</head>

<body>

</body>

</html>