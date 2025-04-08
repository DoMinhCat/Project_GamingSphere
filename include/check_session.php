<?php
if(!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])){
    header("Location: $login_page?:message=Connectez-vous pour accéder à cette page");
    exit();
}
?>