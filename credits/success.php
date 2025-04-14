<?php
require '../vendor/autoload.php';

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
  exit();
}

\Stripe\Stripe::setApiKey('sk_test_51RDpJn2ZZkaFqUsQHx2eH0G1KDxUwLXWqIejLLUbLmvsuDk9hppSPkjUGv9BgOmkEcjHaDHZbbBMNmT2H5NPC1dI00cUBnBfto');

$session_id = $_GET['session_id'];

try {
  $session = \Stripe\Checkout\Session::retrieve($session_id);

  if ($session->payment_status == 'paid') {
    // Ajouter des crédits à l'utilisateur
    $user_id = $_SESSION['user_id'];
    $credits_ajoutes = $session->metadata->credits_ajoutes;

    // Mise à jour des crédits dans la base de données
    include('../database.php');
    $stmt = $bdd->prepare("UPDATE utilisateurs SET credits = credits + ? WHERE id_utilisateurs = ?");
    $stmt->execute([$credits_ajoutes, $user_id]);

    echo "<h2>Le paiement a été effectué avec succès !</h2>";
    echo "<p>Vous avez ajouté $credits_ajoutes crédits à votre compte.</p>";
  } else {
    echo "<h2>Le paiement n'a pas été complété. Veuillez réessayer.</h2>";
  }

} catch (Exception $e) {
  echo "<h2>Une erreur est survenue. Veuillez réessayer.</h2>";
  echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
<a href="/dashboard.php">Retour au tableau de bord</a>
