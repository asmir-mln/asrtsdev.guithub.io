<?php
/* AsArt'sDev | Public payment config */

declare(strict_types=1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/load-secure-config.php';

$config = loadSecureConfig();
$mode = $config['stripe']['mode'] ?? 'test';
$publicKey = $config['stripe'][$mode]['public_key'] ?? null;

if (!$publicKey || strpos($publicKey, 'VOTRE_CLE') !== false) {
    http_response_code(500);
    echo json_encode(['error' => 'Cle publique Stripe non configuree dans api/.env.co']);
    exit;
}

echo json_encode([
    'stripe' => [
        'mode' => $mode,
        'public_key' => $publicKey,
    ],
]);
