<?php
include('../include/database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['token']) && !empty($_POST['token']) && isset($_POST['new_mdp']) && !empty($_POST['new_mdp']) && isset($_POST['confirm_mdp']) && !empty($_POST['confirm_mdp'])) {
        
        if ($_POST['new_mdp'] !== $_POST['confirm_mdp']) {
            header('Location: forgot_mdp.php?message=Les mots de passe ne correspondent pas.');
            exit();
        }
        
        $token = $_POST['token'];
        $new_password = password_hash($_POST['new_mdp'], PASSWORD_DEFAULT); 
        
        try {
            $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW()");
            $stmt->execute(['token' => $token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $stmt = $bdd->prepare("UPDATE utilisateurs SET mot_de_passe = :mdp, reset_mdp_token = NULL, token_expiry = NULL WHERE reset_mdp_token = :token");
                $stmt->execute(['mdp' => $new_password, 'token' => $token]);

                header('Location: forgot_mdp.php?success=1');
                exit();
            } else {
                header('Location: reset_mdp_err.php');
                exit();
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            header('Location: forgot_mdp.php');
            exit();
        }
    } else {
        header('Location: forgot_mdp.php?message=' . urlencode("Veuillez remplir tous les champs demandés"));
        exit();
    }
}
?>
