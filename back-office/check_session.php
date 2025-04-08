<?php
if(!isset($_SESSION['admin']) || empty($_SESSION['admin']) || $_SESSION['admin'] !== true){
    header("Location: $login_page?message=Connectez-vous en tant qu'admin pour accéder à cette page");
    exit();
}
?>