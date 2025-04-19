<?php
session_start();
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  header('location: index.php?message=email_verifie');
  exit();
}
$title = 'Vérification de l\'email';
include('../include/head.php');
?>
<!DOCTYPE html>
<html lang="fr">

</html>

<body>
  <?php
  include("../include/header.php");
  ?>
  <div class="d-flex justify-content-center">
    <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">

      <div class="pb-1 mb-2">
        <h1>Vérification de l'email</h1>
      </div>

      <span class="lato16">Entrez votre adresse email enregistrée et nous vous enverrons un email pour vérifier votre adresse email.</span>

      <?php
      if (isset($_GET['message']) && !empty($_GET['message'])) {
        echo '<div class="lato24 my-2">';
        echo '<p class="m-0 py-2">' . htmlspecialchars($_GET['message']) . '</p>';
      }
      ?>
    </div>
    <form method="post" action="resend_verification.php">
      <div class="d-flex flex-column pt-2 py-3 mb-1 row-gap-1 lato16">
        <input type="email" name="email" placeholder="Votre email" required aria-describedby="emailHelp" class="form-control input_field">

        <div class="d-flex flex-column pt-3">
          <input type="submit" class="btn btn-primary" value="Envoyer le lien de vérification">
        </div>

        <div class="line-with-letters montserrat-titre32 py-3 my-2">
          <span class="line"></span>OU<span class="line"></span>
        </div>

        <div class="d-flex flex-column pt-1">
          <a href="login.php" class="btn btn-success">
            Revenir à la page de connexion
          </a>
        </div>

      </div>
    </form>
  </div>
  </div>

  <?php
  include("../include/footer.php");
  ?>

  <div class="modal fade" id="modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="modal">Lien de vérification envoyé</h1>
        </div>

        <div class="modal-body">
          Veuillez suivez le lien envoyé à votre adresse email enregistrée pour vérifier votre email. Ce lien est valable pour 30 minutes. N'oubliez pas de vérifier votre boîte spam !
          <br>
          Vous devrez peut-être attendre un peu pour que l'email soit envoyé.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const returnParam = urlParams.get('result');

    if (returnParam == 'success') {
      var modal = new bootstrap.Modal(document.getElementById('modal'), {
        keyboard: false
      });
      modal.show();
    }
  </script>
</body>

</html>