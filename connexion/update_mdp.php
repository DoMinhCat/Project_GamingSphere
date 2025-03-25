    <?php
    include('../include/database.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['token']) && !empty($_POST['token']) && isset($_POST['new_mdp']) && !empty($_POST['new_mdp']) && isset($_POST['confirm_mdp']) && !empty($_POST['confirm_mdp'])) {

            $token = $_POST['token'];

            if ($_POST['new_mdp'] !== $_POST['confirm_mdp']) {
                header('Location: reset_mdp.php?token='. $token. '&message=' . urlencode("Veuillez reconfirmer votre mot de passe!"));
                exit();
            }

            if (!preg_match('/[\W_]/', ($_POST['new_mdp']))) {
                header('Location: reset_mdp.php?token='. $token. '&message=' .urldecode("Votre mot de passe ne contient pas un symbole spécial!"));
                exit();
            }
            
            if (!preg_match('/[A-Z]/', ($_POST['new_mdp']))) {
                header('Location: reset_mdp.php?token='. $token. '&message=' .urldecode("Votre mot de passe ne contient pas une lettre majuscule!"));
                exit();
            }

            if  (strlen($_POST['new_mdp']) < 8) {
                header('Location: reset_mdp.php?token='. $token. '&message=' .urldecode("Votre mot de passe doit avoir au moins 8 caractères!"));
                exit();
            }

            if  (!preg_match('/\d/', $_POST['new_mdp'])) {
                header('Location: reset_mdp.php?token='. $token. '&message=' .urldecode("Votre mot de passe ne contient pas de chiffre!"));
                exit();
            }
            
            
            $new_password = password_hash($_POST['new_mdp'], PASSWORD_DEFAULT); 
            
            try {
                $stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW()");
                $stmt->execute(['token' => $token]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $last_passwords = json_decode($user['last3_mdp'], true) ?? [];

                    foreach ($last_passwords as $old_password) {
                        if (password_verify($_POST['new_mdp'], $old_password)) {
                            header('Location: reset_mdp.php?token=' . $token . '&message=' . urlencode("Vous ne pouvez pas réutiliser un ancien mot de passe!"));
                            exit();
                        }
                    }

                    array_unshift($last_passwords, $new_password);
                    if (count($last_passwords) > 3) {
                        array_pop($last_passwords); 
                    }

                    $stmt = $bdd->prepare("UPDATE utilisateurs SET mot_de_passe = :mdp, reset_mdp_token = NULL, token_expiry = NULL, last3_mdp = :last_mdp WHERE reset_mdp_token = :token");
                    $stmt->execute(['mdp' => $new_password, 'token' => $token, 'last_mdp' => json_encode($last_passwords) ]);

                    header('Location: forgot_mdp.php?success=1');
                    exit();
                } else {
                    header('Location: reset_mdp_err.php?message=' .urlencode("Echec de la mise à jour du mot de passe"));
                    exit();
                }

            } catch (PDOException $e) {
                error_log($e->getMessage());
                header('Location: forgot_mdp.php?message=' . urldecode("Impossible de se connecter à la base de donnée"));
                exit();
            }
        } else {
            header('Location: forgot_mdp.php?message=' . urlencode("Veuillez remplir tous les champs demandés"));
            exit();
        }
    } else{
        header('Location: forgot_mdp.php?message=' . urlencode("Ce lien n'est pas disponible"));
            exit();
    }
    ?>
