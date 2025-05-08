<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
$email = $_SESSION['user_email'];
$stream = fopen('../log/log_transaction.txt', 'a+');
$line = date('Y/m/d - H:i:s') . ' - Paiement annulé de ' . $email . "\n";
fputs($stream, $line);
fclose($stream);
?>
<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Annuler le transfert';
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
  <?php include('../include/header.php'); ?>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow rounded border-0 px-5" style="background-color:#FAF9F6;">
          <div class="card-body text-center py-5 d-flex flex-column">
            <h1 class="mt-5 mb-2">Paiement annulé.</h1>
            <span class="lato16 my-3">Vous n'avez pas été débité. Veuillez réessayer si nécessaire.</span>
            <a href='<?= credits_main ?>' class="btn btn-primary"><i class="bi bi-arrow-left mb-3"></i> Retour au Crédits</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php include('../include/footer.php'); ?>
</body>

</html>