<?php
session_start();
require_once __DIR__ . '/../path.php';
function writeLogLine(string $email, bool $success): void
{
    $stream = fopen('../log/log_login.txt', 'a+');
    $line = date('Y/m/d - H:i:s') . ' - Tentative de connexion ' . ($success ? 'réussie' : 'échouée') . ' de ' . $email . "\n";
    fputs($stream, $line);
    fclose($stream);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['email']) && !empty($_POST['email'])) {
        setcookie('email', strtolower($_POST['email']), time() + 24 * 3600);
        $mail = strtolower(trim($_POST['email']));
    }

    if (!isset($_POST['email']) || !isset($_POST['mdp']) || empty($_POST['email']) || empty($_POST['mdp'])) {
        header('location:' . login . '?message=Vous devez remplir les 2 champs.');
        exit;
    }


    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        writeLogLine($_POST['email'], false);
        header('location: ' . login . '?message=Adresse email invalide.');
        exit;
    }


    include('../include/database.php');
    $q_connect = 'SELECT id_utilisateurs, mot_de_passe, pseudo, status_ENUm, email_verifie FROM utilisateurs WHERE email = :email';
    $statement = $bdd->prepare($q_connect);
    $statement->execute([
        'email' => $mail,
    ]);

    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        if ($result['email_verifie'] == 0) {
            header('location:' . login . '?error=email_verification');
            exit();
        }

        if (password_verify($_POST['mdp'], $result['mot_de_passe'])) {
            $_SESSION['user_id'] = $result['id_utilisateurs'];
            $_SESSION['user_email'] = $mail;
            $_SESSION['user_pseudo'] = $result['pseudo'];

            writeLogLine($_POST['email'], true);

            if (isset($result['status_ENUm']) && trim(strtolower($result['status_ENUm'])) === 'admin') {
                $_SESSION['admin'] = true;
                header('location: ../' . index_back);
                exit;
            } else {
                header('location: ../' . index_front . '?success=connected&user_pseudo=' . urlencode($result['pseudo']));
                exit;
            }
        } else {
            writeLogLine($mail, false);
            header('location:' . login . '?message=Identifiants incorrects');
            exit;
        }
    } else {
        writeLogLine($mail, false);
        header('location:' . login . '?message=Identifiants incorrects');
        exit;
    }
}
