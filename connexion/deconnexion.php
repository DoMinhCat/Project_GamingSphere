<?php
session_start();
session_destroy();
setcookie('email', '', time() - 60);
echo "Session détruite. Redirection en cours...";
header('Location: ../index.php');
exit;
?>