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
$title = 'Mot de passe oublié';
include('../include/head.php');
?>

<body>
  <?php
  include("../include/header.php");
  ?>

  <div class="d-flex justify-content-center my-5">
    <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">

      <?php if (!isset($_GET['success']) || empty($_GET['success'])): ?>
        <div class="pb-1 mb-2">
          <h1 class="my-3">Mot de passe oublié ?</h1>
        </div>
        <?php if (isset($_GET['message']) && !empty($_GET['message'])) : ?>
          <div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
            <?= htmlspecialchars($_GET['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif ?>
        <span class="lato16 my-3">Entrez votre email enregistrée et nous vous enverrons un email pour réinitialiser votre mot de passe</span>


        <form method="post" action="/connexion/forgot_verification.php">
          <div class="d-flex flex-column pt-2 py-3 row-gap-1 lato16">
            <input type="email" name="email" placeholder="Votre email" required aria-describedby="emailHelp" class="form-control input_field">

            <div class="d-flex flex-column pt-3">
              <input type="submit" class="btn btn-primary" value="Envoyer le lien de réinitialisation">
            </div>

          </div>
        </form>

        <div class="line-with-letters montserrat-titre32 py-3">
          <span class="line"></span>OU<span class="line"></span>
        </div>

        <div class="pb-2">
          <a href="<?= inscription ?>" id="creer_compte">
            Créer un compte
          </a>
        </div>

      <?php else: ?>
        <h1 class="my-3">Votre mot de passe a été réinitialisé</h1>
        <div class="line-with-letters my-3">
          <span class="line"></span>
        </div>
      <?php endif; ?>

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

  <div class="modal fade" id="send_email" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="send_emailLabel">
            <?php
            if (isset($_GET['return'])) {
              if ($_GET['return'] == 'success') {
                echo 'Email envoyé avec succès';
              } elseif ($_GET['return'] == 'not_found') {
                echo 'Adresse email non trouvée';
              } elseif ($_GET['return'] == 'already_requested') {
                echo 'Demande déjà effectuée';
              }
            }
            ?>
          </h1>
        </div>
        <div class="modal-body">
          <?php
          if (isset($_GET['return'])) {
            if ($_GET['return'] == 'success') {
              echo 'Suivez le lien envoyé à votre email pour réinitialiser votre mot de passe. Ce lien est valable pour 15 minutes. Vous pourrez redemander pour un autre lien après 15 minutes. <br> N\'oubliez pas de verifier votre boîte spam. Vous devrez peut-être attendre un peu pour que l\'e-mail soit envoyé.';
            } elseif ($_GET['return'] == 'not_found') {
              echo 'Veuillez saisir l\'adresse e-mail associée à votre compte.';
            } elseif ($_GET['return'] == 'already_requested') {
              echo 'Une demande de réinitialisation de mot de passe a déjà été envoyée. Veuillez vérifier votre boîte mail et votre boîte spam ou attendez 15 minutes pour demander un autre lien.';
            }
          }
          ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
        </div>

      </div>
    </div>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const returnParam = urlParams.get('return');

    if (returnParam) {
      var modal = new bootstrap.Modal(document.getElementById('send_email'), {
        keyboard: false
      });
      modal.show();
    }
  </script>

</body>

</html>