<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'Mot de passe oublié';
include('../include/head.php'); ?>

<body>
  <?php
  include("../include/header.php");
  ?>


  <div class="d-flex justify-content-center">
    <div class="col-8 col-sm-7 col-md-6 col-lg-7 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
      <div class="pb-1">
        <h1 class="mb-0">Mot de passe oublié ?</h1>
      </div>

      <span class="lato16">Entrez votre email et nous vous enverrons un email pour réinitialiser votre mot de passe</span>

      <div class="lato24 pt-1">
        <?php
        if (isset($_GET['message']) && !empty($_GET['message'])) {
          echo '<p>' . htmlspecialchars($_GET['message']) . '</p>';
        }
        ?>
      </div>
      <form method="post" action="forgot_verification.php">
        <div class="d-flex flex-column pt-2 py-3 row-gap-1 lato16">
          <input type="email" name="email" placeholder="Votre email" required aria-describedby="emailHelp" class="form-control input_field">

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