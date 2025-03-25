
<?php
if (!isset($_GET['token']) || empty($_GET['token'])) {
  header('Location: reset_mdp_err.php');
  exit();
}
$token = $_GET['token'];
include('../include/database.php');

$stmt = $bdd->prepare("SELECT * FROM utilisateurs WHERE reset_mdp_token = :token AND token_expiry > NOW()");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
  header('Location: reset_mdp_err.php');
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


  <div class="d-flex justify-content-center">
    <div class="col-8 col-sm-7 col-md-6 col-lg-7 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
      <div class="pb-3">
        <h1 class="mb-0">Réinitialisation de mot de passe</h1>
      </div>

      <div class="lato24 py-3">
        <?php
        if (isset($_GET['message']) && !empty($_GET['message'])) {
          echo '<p>' . htmlspecialchars($_GET['message']) . '</p>';
        }
        ?>
      </div>

      <form method="post" action="update_mdp.php">
        <div class="d-flex flex-column pt-2 py-3 row-gap-1 lato16">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <label class="form-label">Votre nouveau mot de passe</label>
          <input type="password" name="new_mdp" required class="form-control input_field">
          <div id="mdp_help" class="form-text">Votre mot de passe doit avoir au moins 8 caractères, un chiffre et un caractère spécial</div>

          <label class="form-label">Confirmation de votre nouveau mot de passe</label>
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
        <a href="inscription.php" id="creer_compte">
          Créer un compte
        </a>
      </div>


      <div class="d-flex flex-column pt-1">
        <a href="login.php" class="btn btn-success">
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