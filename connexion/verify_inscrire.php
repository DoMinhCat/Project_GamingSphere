<?php
require('../include/database.php');
if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $bdd->prepare("SELECT id_utilisateurs, inscrire_token_expiry FROM utilisateurs WHERE inscrire_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if (strtotime($user['inscrire_token_expiry']) < time()) {
            header('location: status_verify.php?result=token_invalid');
            exit();
        }
        $stmt = $bdd->prepare("UPDATE utilisateurs SET email_verifie=1, inscrire_token=NULL WHERE id_utilisateurs = ?");
        $stmt->execute([$user['id_utilisateurs']]);

        header('location: status_verify.php?result=success');
        exit();
    } else {
        header('location: status_verify.php?result=token_expire');
        exit();
    }
} else {
    header('location: status_verify.php?result=token_invalid');
    exit();
}
