<?php
$this_page = basename($_SERVER['PHP_SELF']);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        if($this_page=='index.php')
        echo '<script src="../include/check_timeout.js"></script>';
    else echo '<script src="../../include/check_timeout.js"></script>';
    }
    ?>
</head>