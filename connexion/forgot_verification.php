<?php
if (isset($_POST['email']) && !empty($_POST['email']))
    echo '<p>Oki</p>';
    header('forgot_mdp.php');
?>
