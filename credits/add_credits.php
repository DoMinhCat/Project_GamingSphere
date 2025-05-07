<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require('../include/check_timeout.php');
require_once __DIR__ . '/../path.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php $title = 'Ajouter des crédits';
include('../include/head.php');
if (isset($_SESSION['user_email']) && !empty($_SESSION['user_email'])) {
  echo '<script src="../include/check_timeout.js"></script>';
}
?>

<body>
  <?php include('../include/header.php'); ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow rounded py-5 px-3" style="background-color:#FAF9F6;">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Ajouter des crédits</h3>
            <form id="payment-form">
              <div class="mb-3">
                <label for="amount" class="form-label">Montant en € (1€ = 10 crédits)</label>
                <input type="number" class="form-control" id="amount" name="amount" min="1" required>
              </div>
              <div class="d-grid">
                <button type="submit" id="checkout-button" class="btn btn-warning fw-bold">
                  <i class="bi bi-credit-card me-2"></i> Payer
                </button>
              </div>
              <div id="error-message" class="mt-3 text-danger" style="display: none;"></div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://js.stripe.com/v3/"></script>
  <script>
    const stripe = Stripe("pk_test_51RDpJn2ZZkaFqUsQTHcwbnERS9qnFgiqaBnTZgRdqQeT0cc7NC2n5jgszyLGguiGNjlg20xIaSTePX1Vcb4BAftP00Z6b5CsSr");

    document.getElementById('payment-form').addEventListener('submit', function(e) {
      e.preventDefault();

      const amount = document.getElementById('amount').value;


      document.getElementById('error-message').style.display = 'none';
      document.getElementById('error-message').innerHTML = '';

      if (!amount || amount <= 0) {
        document.getElementById('error-message').innerHTML = 'Veuillez entrer un montant valide.';
        document.getElementById('error-message').style.display = 'block';
        return;
      }

      fetch('create_checkout_session.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            amount: amount
          })
        })
        .then(response => {
          if (!response.ok) {
            throw new Error("HTTP error " + response.status);
          }
          return response.json();
        })
        .then(session => {
          if (session.id) {
            return stripe.redirectToCheckout({
              sessionId: session.id
            });
          } else {
            document.getElementById('error-message').innerHTML = 'Erreur lors de la création de la session Stripe.';
            document.getElementById('error-message').style.display = 'block';
          }
        })
        .catch(error => {
          console.error('Erreur :', error);
          document.getElementById('error-message').innerHTML = 'Une erreur est survenue. Veuillez réessayer.';
          document.getElementById('error-message').style.display = 'block';
        });
    });
  </script>

  <?php include('../include/footer.php'); ?>
</body>

</html>