<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';

echo "<h2>Le paiement a été annulé.</h2>";
echo "<p>Vous n'avez pas été débité. Veuillez réessayer si nécessaire.</p>";
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
  <a href="<?= credits_main ?>">Retour au credits</a>
</body>

</html>