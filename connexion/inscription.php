<?php
session_start();
if (!empty($_SESSION['user_email'])) {
  header('Location: ../' . index_front);
  exit();
}
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">

<?php
$title = 'S\'inscrire';
include('../include/head.php');
$error = isset($_GET['error']) ? $_GET['error'] : "";

?>

<body>
  <?php include('../include/header.php');
  if (isset($_GET['message']) && !empty($_GET['message'])) {
    echo '<div class="lato24 my-2">';
    echo '<p class="m-0 py-2">' . htmlspecialchars($_GET['message']) . '</p>';
    echo '</div>';
  }
  ?>
  <div class="d-flex justify-content-center my-5">
    <div class="col-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 justify-content-center text-center p-5 my-5 connexion_box">

      <div class="pb-3 my-3">
        <h1>Inscription</h1>
      </div>

      <form class="row g-3 m-f" method="post" action="inscription_verification.php">
        <div class="col-md-4">
          <input type="text" id="prenom_inscrire" name="prenom" required class="form-control f-inscription" placeholder="Prénom" value="<?php echo isset($_GET['prenom']) ? htmlspecialchars($_GET['prenom']) : ''; ?>">
        </div>
        <div class="col-md-4">
          <input type="text" class="form-control f-inscription" id="nom_inscrire" name="nom" required placeholder="Nom"
            value="<?php echo isset($_GET['nom']) ? htmlspecialchars($_GET['nom']) : ''; ?>">
        </div>
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text" id="inputGroupPrepend2">@</span>
            <input type="text" class="form-control f-inscription <?php echo ($error == 'pseudo_exists') ? 'is-invalid' : ''; ?>" placeholder="Pseudo" id="pseudo_inscrire" aria-describedby="inputGroupPrepend2" name="pseudo" required
              value="<?php echo isset($_GET['pseudo']) ? htmlspecialchars($_GET['pseudo']) : ''; ?>">
            <?php if ($error == 'pseudo_exists') : ?>
              <div class="invalid-feedback">Ce pseudo est déjà associé à un compte.</div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-12 mt-2">
          <?php
          $error = isset($_GET['error']) ? $_GET['error'] : "";
          ?>
          <input type="email" name="email"
            class="form-control f-inscription <?php echo ($error == 'email_exists') ? 'is-invalid' : ''; ?>"
            value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
            id="email_inscrire" placeholder="Adresse email" required>
          <?php if ($error == 'email_exists') : ?>
            <div class="invalid-feedback">Cet e-mail est déjà associé à un compte.</div>
          <?php endif; ?>
        </div>

        <div class="col-12 mb-2">
          <?php
          $error = isset($_GET['error']) ? $_GET['error'] : "";
          ?>
          <input type="password" class="form-control f-inscription <?php echo ($error == 'password_length' || $error == 'password_special_char' || $error == 'password_number' || $error == 'password_upper') ? 'is-invalid' : ''; ?>" aria-describedby="passwordHelpBlock"
            id="mdp_inscrire" name="mot_de_passe" placeholder="Mot de passe" required>
          <div id="passwordHelpBlock" class="form-text">
            Plus que 8 caractères, un caractère spécial, une lettre majuscule et un chiffre.
          </div>
          <?php
          if ($error == 'password_length') {
            echo '<div class="invalid-feedback">Le mot de passe doit avoir au moins 8 caractères, 1 caractère spéciale, 1 lettre majuscule et 1 chiffre</div>';
          } elseif ($error == 'password_special_char') {
            echo '<div class="invalid-feedback">Le mot de passe doit avoir au moins 8 caractères, 1 caractère spéciale, 1 lettre majuscule et 1 chiffre</div>';
          } elseif ($error == 'password_number') {
            echo '<div class="invalid-feedback">Le mot de passe doit avoir au moins 8 caractères, 1 caractère spéciale, 1 lettre majuscule et 1 chiffre</div>';
          }
          ?>

        </div>

        <div class="col-12">
          <div class="col-12 col-lg-9">
            <input
              type="text" name="rue"
              class="form-control f-inscription" id="rue_inscrire" required
              placeholder="Rue"
              value="<?php echo isset($_GET['rue']) ? htmlspecialchars($_GET['rue']) : ''; ?>">
          </div>
          <div class="col-12 col-lg-3">
            <input type="text" placeholder="Code postal" name="code_postal" id="cp_inscrire" class="form-control <?php echo ($error == 'invalid_cp') ? 'is-invalid' : ''; ?>" value="<?php echo isset($_GET['code_postal']) ? htmlspecialchars($_GET['code_postal']) : ''; ?>">
            <?php
            if ($error == 'invalid_cp') {
              echo '<div class="invalid-feedback">Un code postal ne peut contenir que des chiffres</div>';
            }
            ?>
          </div>

          <div class="col-12 col-lg-6">
            <input
              type="text" name="ville"
              placeholder="Ville"
              class="form-control f-inscription" id="ville_inscrire" required
              value="<?php echo isset($_GET['ville']) ? htmlspecialchars($_GET['ville']) : ''; ?>">
          </div>
          <div class="col-12 col-lg-6">
            <select class="form-select f-inscription" name="region"
              id="region_inscrire" required
              value="<?php echo isset($_GET['region']) ? htmlspecialchars($_GET['region']) : ''; ?>">
              <option selected disabled value="">Choisir un région</option>
              <option>Auvergne-Rhône-Alpes</option>
              <option>Bourgogne-Franche-Comté</option>
              <option>Bretagne</option>
              <option>Centre-Val de Loire</option>
              <option>Corse</option>
              <option>Grand-Est</option>
              <option>Hauts-De-France</option>
              <option>Île-de-France</option>
              <option>Normandie</option>
              <option>Nouvelle-Aquitaine</option>
              <option>Occitanie</option>
              <option>Pays de la Loire</option>
              <option>Provence-Alpes-Côte d'Azur (PACA)</option>
            </select>
          </div>
        </div>

        <div class="col-12 mt-3">
          <?php
          $questions = [
            "Combien font 3 + 5 ?" => 8,
            "Quelle été la couleur du cheval blanc d'Henry V ? (en un mot)" => "blanc",
            "Quelle est la couleur du ciel (en un mot) ?" => "bleu",
            "Combien font 10 - 4 ?" => 6,
          ];
          $question_keys = array_keys($questions);
          $random_question = $question_keys[array_rand($question_keys)];
          $_SESSION['captcha_answer'] = strtolower(trim($questions[$random_question]));
          ?>
          <p class="text-start" id="captcha_question"><?= 'Question captcha : ' . htmlspecialchars($random_question) ?></p>
          <input type="text" id="captcha_answer" name="captcha_answer" class="form-control f-inscription mb-2 <?php echo ($error == 'captcha_invalid') ? 'is-invalid' : ''; ?>" required>
          <?php if ($error == 'captcha_invalid') {
            echo '<div class="invalid-feedback">La réponse au CAPTCHA est incorrecte.</div>';
          } ?>
          <div class="form-check text-start">
            <input class="form-check-input" type="checkbox" value="1" id="checkbox_inscrire" required>
            <label class="form-check-label" for="checkbox_inscrire">
              Accepter les conditions générales d'utilisations
            </label>
          </div>
        </div>

        <div class="col-12 mt-4">
          <input type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
          Créer mon compte
          </input>
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
          <h1 class="modal-title fs-5" id="modal">Vérification de l'e-mail requise</h1>
        </div>

        <div class="modal-body">
          Votre compte a été crée. <br>Veuillez suivez le lien envoyé à votre adresse email enregistrée pour vérifier votre email. Ce lien est valable pour 30 minutes. N'oubliez pas de vérifier votre boîte spam !
          <br>
          Vous devrez peut-être attendre un peu pour que l'email soit envoyé.
        </div>
        <div class="modal-footer">
          <a href="<?= resend_verify_inscrire ?>" type="button" class="btn btn-primary">Renvoyer l'email</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
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