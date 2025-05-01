<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
include('../include/database.php');

try {
  $stmt = $bdd->prepare("SELECT pseudo FROM utilisateurs WHERE id_utilisateurs = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $userData = $stmt->fetch(PDO::FETCH_ASSOC);

  // Pour le débogage
  // var_dump($userData);

  if (!$userData) {
    throw new Exception("Utilisateur introuvable.");
  }

  $pseudo = $userData['pseudo'];

  $stmt = $bdd->prepare("SELECT credits FROM credits WHERE user_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $creditData = $stmt->fetch(PDO::FETCH_ASSOC);


  if (!$creditData) {
    $insert = $bdd->prepare("INSERT INTO credits (user_id, credits) VALUES (?, 0)");
    $insert->execute([$_SESSION['user_id']]);
    $credits = 0;
  } else {
    $credits = (int)$creditData['credits'];
  }
} catch (Exception $e) {
  $credits = 0;
  $pseudo = "Inconnu";
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Mes Crédits'; ?>
<?php include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
  <?php include('../include/header.php'); ?>
  <div class="container py-5">
    <div class="card shadow-lg p-4">
      <h2 class="mb-4 text-center">💻 Espace Crédits</h2>

      <div class="text-center mb-3">
        <h4>Bonjour <strong><?= htmlspecialchars($pseudo ?? "Utilisateur inconnu", ENT_QUOTES, 'UTF-8') ?></strong> !</h4>
        <p>Voici votre solde actuel :</p>
        <h3 class="text-warning">
          <i class="bi bi-wallet2"></i>
          <?= htmlspecialchars((string)$credits, ENT_QUOTES, 'UTF-8') ?> crédits
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