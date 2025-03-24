<!DOCTYPE html>
<html lang="en">
<?php
$title = 'Erreur de réinitialisation du mot de passe';
include('../include/head.php')
?>
<body>
    <div class="d-flex justify-content-center">
    <div class="col-8 col-sm-7 col-md-6 col-lg-7 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">
      <div class="pb-3">
        <h1 class="mb-0">Erreur</h1>
      </div>

      <div class="lato24 pt-1">
        <?php
        if (isset($_GET['message']) && !empty($_GET['message'])) {
          echo '<p>' . htmlspecialchars($_GET['message']) . '</p>';
        }
        ?>
      </div>
      
      <div class="line-with-letters montserrat-titre32">
        <span class="line"></span>
      </div>

      <div class="py-2">
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
  include('../include/footer.php');
  ?>
</body>
</html>