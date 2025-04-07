<?php
include('../../include/database.php');
require('../../include/check_timeout.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gestions des canaux de communications</title>

    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="../../includes/check_timeout.js"></script>';
    }
    ?>
</head>

<body>

</body>

</html>