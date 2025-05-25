<?php
session_start();
require_once __DIR__ . '/../path.php';
if (!empty($_SESSION['user_email'])) {
  header('Location: ../' . index_front);
  exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Erreur de réinitialisation du mot de passe';
include('../include/head.php')
?>

<body>
  <div class="d-flex justify-content-center my-5">
    <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
      <div class="pb-3">
        <h1 class="mb-0">Erreur</h1>
      </div>

      <?php
      if (isset($_GET['message']) && !empty($_GET['message'])) {
        echo '<div class="lato24 my-2">';
        echo '<p class="m-0 py-2">' . htmlspecialchars($_GET['message']) . '</p>';
      }
      ?>

      <div class="line-with-letters montserrat-titre32 my-2">
        <span class="line"></span>
      </div>

      <div class="py-2 mb-1">
        <a href="<?= inscription ?>" id="creer_compte">
          Créer un compte
        </a>
      </div>


      <div class="d-flex flex-column pt-1">
        <a href="<?= forgot_mdp ?>" class="btn btn-success">
          Réinitialiser mon mot de passe
        </a>
        <a href="<?= login ?>" class="btn btn-success mt-2">
          Revenir à la page de connexion
        </a>
      </div>
    </div>
  </div>
  <?php
  include('../include/footer.php');
  ?>
</body>

</html>