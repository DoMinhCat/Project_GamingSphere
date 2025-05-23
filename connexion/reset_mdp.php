<?php
session_start();
require_once __DIR__ . '/../path.php';
if (!empty($_SESSION['user_email'])) {
  header('Location: ../' . index_front);
  exit();
}

if (!isset($_GET['token']) || empty($_GET['token'])) {
  header('Location:' . reset_mdp_err . '?message=' . urldecode("Cette page est actuellement indisponible"));
  exit();
}
$token = $_GET['token'];
include('../include/database.php');

$stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW()");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  header('Location:' . reset_mdp_err . '?message=' . urldecode("Cette page est actuellement indisponible"));
  exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
$title = 'Réinitilisation de mot de passe';
include('../include/head.php')
?>

<body>
  <?php
  include("../include/header.php");
  ?>


  <div class="d-flex justify-content-center my-5">
    <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">

      <div class="pb-3">
        <h1 class="mb-0">Réinitialisation de mot de passe</h1>
      </div>


      <?php
      if (isset($_GET['message']) && !empty($_GET['message'])) {
        echo '<div class="lato24 my-2">';
        echo '<p class="m-0 py-2">' . htmlspecialchars($_GET['message']) . '</p>';
        echo '</div>';
      }
      ?>


      <form method="post" action="update_mdp.php">
        <div class="d-flex flex-column pt-2 py-3 row-gap-1 lato16">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <label class="form-label text-start lato24  ">Votre nouveau mot de passe</label>
          <input type="password" name="new_mdp" required class="form-control input_field">
          <div id="mdp_help" class="form-text text-start">Votre mot de passe doit contenir au moins 8 caractères, une lettre majuscule, un chiffre, un symbole spécial et ne peut pas être l'un des 3 mots de passe les plus récents</div>

          <label class="form-label text-start lato24">Confirmation de votre nouveau mot de passe</label>
          <input type="password" name="confirm_mdp" required class="form-control input_field">

          <div class="d-flex flex-column pt-3">
            <input type="Submit" class="btn btn-primary" value="Réinitialiser mon mot de passe">

          </div>
        </div>
      </form>

      <div class="line-with-letters montserrat-titre32">
        <span class="line"></span>OU<span class="line"></span>
      </div>

      <div class="pb-2">
        <a href="<?= inscription ?>" id="creer_compte">
          Créer un compte
        </a>
      </div>


      <div class="d-flex flex-column pt-1">
        <a href="<?= login ?>" class="btn btn-success">
          Revenir à la page de connexion
        </a>
      </div>
    </div>
  </div>

  <?php
  include("../include/footer.php");
  ?>
</body>

</html>