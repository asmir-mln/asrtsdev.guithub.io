<?php
/* AsArt'sDev | Secure config loader */

declare(strict_types=1);

/**
 * Charge une configuration sensible depuis api/.env.co (valeurs base64),
 * avec fallback vers api/config.php si present.
 */
function loadSecureConfig(): array
{
    $envCoPath = __DIR__ . '/.env.co';
    if (is_file($envCoPath)) {
        $flat = parseEnvCoFile($envCoPath);
        $mode = $flat['STRIPE_MODE'] ?? 'test';

        return [
            'stripe' => [
                'mode' => $mode,
                'test' => [
                    'public_key' => $flat['STRIPE_TEST_PUBLIC_KEY'] ?? null,
                    'secret_key' => $flat['STRIPE_TEST_SECRET_KEY'] ?? null,
                    'webhook_secret' => $flat['STRIPE_TEST_WEBHOOK_SECRET'] ?? null,
                ],
                'live' => [
                    'public_key' => $flat['STRIPE_LIVE_PUBLIC_KEY'] ?? null,
                    'secret_key' => $flat['STRIPE_LIVE_SECRET_KEY'] ?? null,
                    'webhook_secret' => $flat['STRIPE_LIVE_WEBHOOK_SECRET'] ?? null,
                ],
            ],
            'paypal' => [
                'client_id' => $flat['PAYPAL_CLIENT_ID'] ?? null,
                'client_secret' => $flat['PAYPAL_CLIENT_SECRET'] ?? null,
            ],
            'bank' => [
                'beneficiary' => $flat['BANK_BENEFICIARY'] ?? null,
                'iban' => $flat['BANK_IBAN'] ?? null,
                'bic' => $flat['BANK_BIC'] ?? null,
                'bank_name' => $flat['BANK_NAME'] ?? null,
            ],
        ];
    }

    $configPath = __DIR__ . '/config.php';
    if (is_file($configPath)) {
        $config = require $configPath;
        return is_array($config) ? $config : [];
    }

    return [];
}

/**
 * Format attendu du fichier .env.co
 * KEY=base64(value)
 */
function parseEnvCoFile(string $path): array
{
    $result = [];
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $result;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $rawValue = trim($parts[1]);
        if ($key === '') {
            continue;
        }

        $decoded = base64_decode($rawValue, true);
        $result[$key] = $decoded === false ? $rawValue : $decoded;
    }

    return $result;
}
