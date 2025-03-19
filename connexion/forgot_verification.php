<?php
if (isset($_POST['email']) && !empty($_POST['email'])) {
    echo '<h1>Email reçu</h1>';
    header('forgot_mdp.php');
    exit();
}
