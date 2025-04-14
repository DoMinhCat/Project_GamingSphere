<?php
include('../include/database.php');
session_start();

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php');
  exit();
}

try {
  $stmt = $bdd->prepare("SELECT credits, pseudo FROM utilisateurs WHERE id_utilisateurs = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    throw new Exception("Utilisateur introuvable.");
  }

  $credits = $user['credits'];
  $pseudo = $user['pseudo'];
} catch (Exception $e) {
  $credits = 0;
  $pseudo = "Inconnu";
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Mes Crédits';?>
<?php include('../include/head.php');?>

<body>
<?php include('../include/header.php'); ?>
  <div class="container py-5">
    <div class="card shadow-lg p-4">
      <h2 class="mb-4 text-center">👛 Espace Crédits</h2>
      
      <div class="text-center mb-3">
        <h4>Bonjour <strong><?= htmlspecialchars($pseudo) ?></strong> !</h4>
        <p>Voici votre solde actuel :</p>
        <h3 class="text-warning">
          <i class="bi bi-wallet2"></i>
          <?= htmlspecialchars($credits) ?> crédits
        </h3>
      </div>

      <div class="d-flex justify-content-center">
        <a href="add_credits.php" class="btn btn-warning btn-lg">
          <i class="bi bi-plus-circle"></i> Ajouter des crédits
        </a>
      </div>
    </div>
  </div>
<?php include('../include/footer.php'); ?>
</body>
</html>
