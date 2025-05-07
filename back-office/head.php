<?php
$this_page = basename($_SERVER['PHP_SELF']);
require_once  __DIR__ . '/../path.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/back-office/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php
    if (isset($_SESSION['admin']) && !empty($_SESSION['admin'])) {
        echo '<script src="/include/check_timeout.js"></script>';
    }
    ?>
    <?php if (!empty($_SESSION['user_email'])): ?>
        <script src="/status_user/status_online.js"></script>
    <?php endif; ?>
</head>