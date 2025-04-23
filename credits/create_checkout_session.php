<?php
require '../vendor/autoload.php';

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
  exit();
}

\Stripe\Stripe::setApiKey('sk_test_51RDpJn2ZZkaFqUsQHx2eH0G1KDxUwLXWqIejLLUbLmvsuDk9hppSPkjUGv9BgOmkEcjHaDHZbbBMNmT2H5NPC1dI00cUBnBfto');


$data = json_decode(file_get_contents('php://input'), true);
$amount = isset($data['amount']) ? (float)$data['amount'] : 0;
$amountInCents = intval($amount * 100);

$credits_ajoutes = $amount * 10;

try {
  $checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [
      [
        'price_data' => [
          'currency' => 'eur',
          'product_data' => [
            'name' => 'Ajout de crédits',
          ],
          'unit_amount' => $amountInCents, // Montant en centimes
        ],
        'quantity' => 1,
      ],
    ],
    'mode' => 'payment',
    'success_url' => 'https://213.32.90.110/credits/success.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://213.32.90.110/credits/cancel.php',
    'metadata' => [
      'credits_ajoutes' => $credits_ajoutes // Ajout des crédits dans les métadonnées
    ]
  ]);

  header('Content-Type: application/json');
  echo json_encode(['id' => $checkout_session->id]);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Erreur lors de la création de la session : ' . $e->getMessage()]);
}
?>
