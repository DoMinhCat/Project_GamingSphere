<?php
$timeout_duree = 60*30;
if (isset($_SESSION['user_email']) || isset($_SESSION['admin'])) {
    if (isset($_SESSION['actif'])) {
        if (time() - $_SESSION['actif'] > $timeout_duree) {
            if ($this_page == "index.php")
                header("Location: ../connexion/session_timeout.php");
            else
                header("Location: connexion/session_timeout.php");
            exit();
        }
    }
    $_SESSION['actif'] = time();
}
