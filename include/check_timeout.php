<?php
require_once('/path.php');
$timeout_duree = 60 * 30;

if (isset($_SESSION['actif']) && (time() - $_SESSION['actif']) > $timeout_duree) {
    session_unset();
    session_destroy();
    header("Location: " . session_timeout);
    exit();
}

$_SESSION['actif'] = time();
