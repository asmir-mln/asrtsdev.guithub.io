<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Méthode non autorisée.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$adminEmail = 'asartdev.contact@gmail.com';

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$type = trim((string) ($_POST['partnership_type'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));
$sourcePage = trim((string) ($_POST['source_page'] ?? 'site'));
$confidentialite = isset($_POST['confidentialite']) ? 'Oui' : 'Non';

if ($name === '' || $email === '' || $type === '' || $message === '') {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Veuillez remplir tous les champs obligatoires.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Adresse email invalide.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$allowedTypes = [
    'investisseur_prive',
    'investisseur_public',
    'fournisseur',
    'mecenat',
    'academique',
    'international',
    'autre'
];

if (!in_array($type, $allowedTypes, true)) {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Type de partenariat non valide.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$typeLabels = [
    'investisseur_prive' => 'Investisseur privé',
    'investisseur_public' => 'Partenaire public',
    'fournisseur' => 'Fournisseur technologique',
    'mecenat' => 'Mécénat / Soutien associatif',
    'academique' => 'Partenaire académique',
    'international' => 'Partenaire international',
    'autre' => 'Autre'
];

$subject = '[AsArt\'sDev] Nouveau contact professionnel - ' . ($typeLabels[$type] ?? $type);

$bodyLines = [
    "Nouveau message professionnel reçu via le formulaire partenariat.",
    "",
    "Nom / Organisme : {$name}",
    "Email : {$email}",
    'Téléphone : ' . ($phone !== '' ? $phone : 'Non renseigné'),
    'Type de partenariat : ' . ($typeLabels[$type] ?? $type),
    "Confidentialité acceptée : {$confidentialite}",
    "Page source : {$sourcePage}",
    "",
    "Message :",
    $message,
    "",
    'Date : ' . date('Y-m-d H:i:s')
];

$body = implode("\n", $bodyLines);

$safeReplyTo = str_replace(["\r", "\n"], '', $email);
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: AsArtsDev Site <no-reply@asartsdev.local>',
    'Reply-To: ' . $safeReplyTo,
    'X-Mailer: PHP/' . phpversion()
];

$sent = @mail($adminEmail, $subject, $body, implode("\r\n", $headers));

if (!$sent) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Erreur lors de l\'envoi. Réessayez ou contactez asartdev.contact@gmail.com.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => 'Votre demande professionnelle a été envoyée. Nous vous répondrons rapidement.'
], JSON_UNESCAPED_UNICODE);
