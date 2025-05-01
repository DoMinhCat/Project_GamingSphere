<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
require '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RDpJn2ZZkaFqUsQHx2eH0G1KDxUwLXWqIejLLUbLmvsuDk9hppSPkjUGv9BgOmkEcjHaDHZbbBMNmT2H5NPC1dI00cUBnBfto');
?>


<!DOCTYPE html>
<html lang="fr">
<?php
$session_id = $_GET['session_id'] ?? null;
$title = "Paiement réussi";
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body class="bg-light">
  <?php include('../include/header.php'); ?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow rounded border-0">
          <div class="card-body text-center py-5">
            <?php
            try {
              if (!$session_id) {
                throw new Exception("Aucune session Stripe n’a été spécifiée.");
              }

              $session = \Stripe\Checkout\Session::retrieve($session_id);

              if ($session->payment_status === 'paid') {
                $user_id = $_SESSION['user_id'];
                $credits_ajoutes = isset($session->metadata->credits_ajoutes) ? (int)$session->metadata->credits_ajoutes : 0;

                if ($credits_ajoutes > 0) {
                  include('../include/database.php');
                  $stmt = $bdd->prepare("UPDATE credits SET credits = credits + ? WHERE user_id = ?");
                  $stmt->execute([$credits_ajoutes, $user_id]);
            ?>
                  <i class="bi bi-check-circle text-success display-1"></i>
                  <h2 class="mt-4 text-success">Paiement confirmé !</h2>
                  <p class="lead">Merci pour votre achat. <strong><?= $credits_ajoutes ?></strong> crédits ont été ajoutés à votre compte.</p>
                <?php
                } else {
                ?>
                  <i class="bi bi-exclamation-triangle-fill text-warning display-1"></i>
                  <h2 class="mt-4 text-warning">Problème avec les crédits</h2>
                  <p class="lead">Le paiement a été validé mais le nombre de crédits n’a pas pu être déterminé.</p>
                <?php
                }
              } else {
                ?>
                <i class="bi bi-x-circle text-danger display-1"></i>
                <h2 class="mt-4 text-danger">Paiement incomplet</h2>
                <p class="lead">Le paiement n’a pas été complété. Aucun crédit n’a été ajouté.</p>
              <?php
              }
            } catch (Exception $e) {
              ?>
              <i class="bi bi-bug-fill text-danger display-1"></i>
              <h2 class="mt-4 text-danger">Erreur technique</h2>
              <p class="lead">Une erreur est survenue : <?= htmlspecialchars($e->getMessage()) ?></p>
            <?php
            }
            ?>
            <a href="../index.php" class="btn btn-primary mt-4"><i class="bi bi-arrow-left"></i> Retour à l'Accueil</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include('../include/footer.php'); ?>
</body>

</html>