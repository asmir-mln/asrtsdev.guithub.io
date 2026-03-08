<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * API de création de Payment Intent pour Stripe
 * AsArt'sDev - Système de paiement sécurisé
 */

// Configuration CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
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

// Charger la bibliothèque Stripe
// Installation : composer require stripe/stripe-php
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

// Récupérer les données POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Valider les données
if (!isset($data['amount']) || !isset($data['currency'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    exit;
}

$amount = intval($data['amount']); // Montant en centimes
$currency = $data['currency'];
$description = $data['description'] ?? 'Commande AsArt\'sDev';
$metadata = $data['metadata'] ?? [];

// Valider le montant
if ($amount < 50) { // Minimum 0.50€
    http_response_code(400);
    echo json_encode(['error' => 'Le montant minimum est de 0,50 €']);
    exit;
}

try {
    // Créer un Payment Intent
    $paymentIntent = $paymentIntentClass::create([
        'amount' => $amount,
        'currency' => $currency,
        'description' => $description,
        'metadata' => $metadata,
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
        'receipt_email' => $metadata['email'] ?? null,
    ]);
    
    // Logger la transaction (optionnel)
    logTransaction([
        'payment_intent_id' => $paymentIntent->id,
        'amount' => $amount,
        'currency' => $currency,
        'status' => $paymentIntent->status,
        'metadata' => $metadata,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Retourner le client secret
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
        'paymentIntentId' => $paymentIntent->id
    ]);
    
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()]);
}

/**
 * Logger les transactions dans un fichier
 */
function logTransaction($data) {
    $logFile = __DIR__ . '/../logs/transactions.log';
    $logDir = dirname($logFile);
    
    // Créer le dossier logs s'il n'existe pas
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . ' | ' . json_encode($data) . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}
?>


