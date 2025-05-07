<?php
session_start();
$login_page = '../connexion/login.php';
require('../include/check_session.php');
require_once __DIR__ . '/../path.php';
require '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RDpJn2ZZkaFqUsQHx2eH0G1KDxUwLXWqIejLLUbLmvsuDk9hppSPkjUGv9BgOmkEcjHaDHZbbBMNmT2H5NPC1dI00cUBnBfto');

$data = json_decode(file_get_contents('php://input'), true);
$amount = isset($data['amount']) ? (float)$data['amount'] : 0;
$amountInCents = intval($amount * 100);

$credits_ajoutes = $amount * 10;
$email = $_SESSION['user_email'];
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
          'unit_amount' => $amountInCents,
        ],
        'quantity' => 1,
      ],
    ],
    'mode' => 'payment',
    'success_url' => 'https://gamingsphere.duckdns.org/' . success . '?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'https://gamingsphere.duckdns.org/' . cancel,
    'metadata' => [
      'credits_ajoutes' => $credits_ajoutes
    ]
  ]);

  header('Content-Type: application/json');
  echo json_encode(['id' => $checkout_session->id]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Erreur lors de la création de la session : ' . $e->getMessage()]);
}
