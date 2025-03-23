<?php
include('../include/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW()");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $bdd->prepare("UPDATE utilisateurs SET mot_de_passe = :mdp, reset_token = NULL, token_expiry = NULL WHERE reset_mdp_token = :token");
        $stmt->execute(['password' => $new_password, 'token' => $token]);

        header('Location: forgot_mdp.php?success=1');
        exit();
    }
    else{
        header('Location: reset_mdp_err.php');
        exit();
    }

}
?>