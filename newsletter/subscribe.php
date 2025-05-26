<?php
require_once('../include/database.php');
require_once __DIR__ . '/../path.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim(strtolower($_POST['email']));
    $token = bin2hex(random_bytes(32));
    try {
        $stmt = $bdd->prepare("SELECT newsletter_sub from utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check['newsletter_sub'] == 1) {
            header('location:' . index_front . '?error=' . urlencode("Vous êtes déjà inscrit(e) à notre newsletter !"));
            exit;
        } else {
            $stmt = $bdd->prepare("UPDATE utilisateurs SET newsletter_date=?, newsletter_sub = 1, unsubscribe_token = ? WHERE email = ?");
            $today = date("d/m/Y");
            $stmt->execute([$today, $token, $email]);

            if ($stmt->rowCount() > 0) {
                header('location:' . index_front . '?newsletter=' . urlencode("Vous êtes maintenant inscrit(e) à notre newsletter !"));
            } else {
                header('location:' . index_front . '?error=' . urlencode("Adresse email inconnue. Avez-vous déjà un compte ?"));
            }
            exit;
        }
    } catch (PDOException) {
        header('location:' . index_front . '?message=bdd');
        exit;
    }
}
