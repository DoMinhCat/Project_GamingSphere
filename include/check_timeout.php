<?php
$timeout_duree = 600;
if (isset($_SESSION['actif'])) {
    if (time() - $_SESSION['actif'] > $timeout_duree) {
        session_unset();
        session_destroy();
        if ($this_page == "index.php")
            header("Location: ../connexion/session_timeout.php");
        else
            header("Location: connexion/session_timeout.php");
        exit();
    }
}
$_SESSION['actif'] = time();
?>