<?php
session_start();
require 'vendor/autoload.php'; // Inclure la bibliothèque Stripe

// Clé secrète de Stripe (cliquez sur votre dashboard Stripe pour la trouver)
\Stripe\Stripe::setApiKey("sk_test_51RDpJn2ZZkaFqUsQHx2eH0G1KDxUwLXWqIejLLUbLmvsuDk9hppSPkjUGv9BgOmkEcjHaDHZbbBMNmT2H5NPC1dI00cUBnBfto"); // Remplacez par votre clé secrète

// Vérifiez que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

// Récupérez le montant envoyé par le client
$data = json_decode(file_get_contents('php://input'), true);
$amount = $data['amount'];  // Montant en euros
$amountInCents = $amount * 100;  // Convertir en cents

// Avant la création de la session Stripe
error_log("Montant : " . $amountInCents);

// Créez la session de paiement
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
        'success_url' => 'https://votre-site.com/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'https://votre-site.com/cancel.php',
    ]);

    // Retourner l'ID de la session pour rediriger l'utilisateur vers Stripe
    echo json_encode(['id' => $checkout_session->id]);

} catch (Exception $e) {
    error_log("Erreur Stripe : " . $e->getMessage()); // Log d'erreur
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la création de la session : ' . $e->getMessage()]);
    exit();
}
?>
