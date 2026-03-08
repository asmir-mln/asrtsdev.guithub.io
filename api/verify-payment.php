<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * Vérification du statut d'un paiement
 * AsArt'sDev
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function loadStripeSecretKey() {
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) {
        return null;
    }

    $config = require $configPath;
    $mode = $config['stripe']['mode'] ?? 'test';
    return $config['stripe'][$mode]['secret_key'] ?? null;
}

$stripeSecretKey = loadStripeSecretKey();
if (!$stripeSecretKey || strpos($stripeSecretKey, 'VOTRE_CLE') !== false) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration Stripe manquante. Créez api/config.php avec vos clés.']);
    exit;
}

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/stripe-php/init.php')) {
    require_once __DIR__ . '/stripe-php/init.php';
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Bibliothèque Stripe introuvable. Exécutez composer require stripe/stripe-php']);
    exit;
}

$stripeClass = '\\Stripe\\Stripe';
$paymentIntentClass = '\\Stripe\\PaymentIntent';

$stripeClass::setApiKey($stripeSecretKey);

// Récupérer l'ID du Payment Intent
$paymentIntentId = $_GET['payment_intent_id'] ?? $_POST['payment_intent_id'] ?? null;

if (!$paymentIntentId) {
    http_response_code(400);
    echo json_encode(['error' => 'Payment Intent ID manquant']);
    exit;
}

try {
    // Récupérer le Payment Intent
    $paymentIntent = $paymentIntentClass::retrieve($paymentIntentId);
    
    $response = [
        'id' => $paymentIntent->id,
        'status' => $paymentIntent->status,
        'amount' => $paymentIntent->amount / 100,
        'currency' => strtoupper($paymentIntent->currency),
        'created' => date('Y-m-d H:i:s', $paymentIntent->created),
        'metadata' => $paymentIntent->metadata
    ];
    
    // Ajouter des informations supplémentaires selon le statut
    if ($paymentIntent->status === 'succeeded') {
        $response['message'] = 'Paiement réussi';
        $response['charges'] = $paymentIntent->charges->data;
    } elseif ($paymentIntent->status === 'requires_payment_method') {
        $response['message'] = 'En attente de paiement';
    } elseif ($paymentIntent->status === 'processing') {
        $response['message'] = 'Paiement en cours de traitement';
    } elseif ($paymentIntent->status === 'requires_action') {
        $response['message'] = 'Action requise (3D Secure)';
    } elseif ($paymentIntent->status === 'canceled') {
        $response['message'] = 'Paiement annulé';
    }
    
    echo json_encode($response);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}
?>


