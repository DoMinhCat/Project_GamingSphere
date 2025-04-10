<?php
$timeout_duree = 60*30;

if (isset($_SESSION['actif']) && (time() - $_SESSION['actif']) > $timeout_duree) {
    session_unset();
    session_destroy();
    header("Location: " . $_SERVER['DOCUMENT_ROOT'] . "/PA/connexion/session_timeout.php");
    exit();
}

$_SESSION['actif'] = time();
?>