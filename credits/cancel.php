<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
  exit();
}

echo "<h2>Le paiement a été annulé.</h2>";
echo "<p>Vous n'avez pas été débité. Veuillez réessayer si nécessaire.</p>";
?>
<a href="/dashboard.php">Retour au tableau de bord</a>
