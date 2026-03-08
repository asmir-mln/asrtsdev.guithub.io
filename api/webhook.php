<?php
/* AsArt'sDev | Signature invisible | ASmir Milia | ASARTSDEV_SIGNATURE_INVISIBLE */
/**
 * Webhook Stripe - Gestion des événements de paiement
 * AsArt'sDev
 */

// Configuration
header('Content-Type: application/json');

function loadStripeConfig() {
    $configPath = __DIR__ . '/config.php';
    if (!file_exists($configPath)) {
        return null;
    }

    $config = require $configPath;
    $mode = $config['stripe']['mode'] ?? 'test';
    return [
        'secret_key' => $config['stripe'][$mode]['secret_key'] ?? null,
        'webhook_secret' => $config['stripe'][$mode]['webhook_secret'] ?? null
    ];
}

$stripeConfig = loadStripeConfig();
$stripeSecretKey = $stripeConfig['secret_key'] ?? null;
$stripeWebhookSecret = $stripeConfig['webhook_secret'] ?? null;

if (!$stripeSecretKey || strpos($stripeSecretKey, 'VOTRE_CLE') !== false || !$stripeWebhookSecret || strpos($stripeWebhookSecret, 'VOTRE') !== false) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration Stripe webhook manquante. Créez api/config.php avec vos clés.']);
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
$webhookClass = '\\Stripe\\Webhook';

$stripeClass::setApiKey($stripeSecretKey);

// Récupérer le corps de la requête et la signature
$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    // Vérifier la signature du webhook
    $event = $webhookClass::constructEvent(
        $payload,
        $sig_header,
        $stripeWebhookSecret
    );
    
} catch (\UnexpectedValueException $e) {
    // Payload invalide
    http_response_code(400);
    exit;
} catch (Throwable $e) {
    // Signature invalide ou erreur Stripe
    http_response_code(400);
    exit;
}

// Gérer les différents types d'événements
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        handlePaymentSuccess($paymentIntent);
        break;
        
    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;
        handlePaymentFailure($paymentIntent);
        break;
        
    case 'charge.refunded':
        $charge = $event->data->object;
        handleRefund($charge);
        break;
        
    default:
        // Événement non géré
        error_log('Événement Stripe non géré : ' . $event->type);
}

http_response_code(200);
echo json_encode(['status' => 'success']);

/**
 * Gérer le succès du paiement
 */
function handlePaymentSuccess($paymentIntent) {
    $metadata = $paymentIntent->metadata;
    
    // Logger la transaction
    logWebhookEvent([
        'type' => 'payment_success',
        'payment_intent_id' => $paymentIntent->id,
        'amount' => $paymentIntent->amount / 100,
        'currency' => $paymentIntent->currency,
        'email' => $metadata->email ?? 'N/A',
        'produit' => $metadata->produit ?? 'N/A',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // Envoyer un email de confirmation
    if (isset($metadata->email)) {
        sendConfirmationEmail($metadata->email, [
            'produit' => $metadata->produit ?? 'Produit AsArt\'sDev',
            'montant' => $paymentIntent->amount / 100,
            'date' => date('d/m/Y H:i')
        ]);
    }
    
    // TODO : Ajouter la commande à la base de données
    // saveOrderToDatabase($paymentIntent);
    
    // TODO : Envoyer le produit numérique par email
    // sendDigitalProduct($metadata->email, $metadata->produit);
}

/**
 * Gérer l'échec du paiement
 */
function handlePaymentFailure($paymentIntent) {
    logWebhookEvent([
        'type' => 'payment_failure',
        'payment_intent_id' => $paymentIntent->id,
        'error' => $paymentIntent->last_payment_error->message ?? 'Erreur inconnue',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    // TODO : Notifier l'administrateur
}

/**
 * Gérer les remboursements
 */
function handleRefund($charge) {
    logWebhookEvent([
        'type' => 'refund',
        'charge_id' => $charge->id,
        'amount' => $charge->amount_refunded / 100,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Logger les événements webhook
 */
function logWebhookEvent($data) {
    $logFile = __DIR__ . '/../logs/webhooks.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . ' | ' . json_encode($data) . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Envoyer un email de confirmation
 */
function sendConfirmationEmail($email, $data) {
    $subject = 'Confirmation de votre commande AsArt\'sDev';
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Merci pour votre commande !</h1>
            </div>
            <div class='content'>
                <h2>Confirmation de paiement</h2>
                <p>Bonjour,</p>
                <p>Nous avons bien reçu votre paiement pour :</p>
                <ul>
                    <li><strong>Produit :</strong> {$data['produit']}</li>
                    <li><strong>Montant :</strong> {$data['montant']} EUR</li>
                    <li><strong>Date :</strong> {$data['date']}</li>
                </ul>
                <p>Votre commande sera traitée dans les plus brefs délais.</p>
                <p>Cordialement,<br>L'équipe AsArt'sDev</p>
            </div>
            <div class='footer'>
                <p>© 2026 AsArt'sDev – créé par ASmir Milia</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: contact@asartsdev.com',
        'Reply-To: contact@asartsdev.com'
    ];
    
    @mail($email, $subject, $message, implode("\r\n", $headers));
}
?>


