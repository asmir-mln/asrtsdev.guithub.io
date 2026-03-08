<?php
/**
 * AsArt'sDev Orders API
 * Gestion des commandes sans paiement en ligne
 * Open-source - MIT License
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Configuration
define('ORDERS_FILE', __DIR__ . '/data/orders.json');
define('ADMIN_EMAIL', 'asartdev.contact@gmail.com');

// Créer le dossier data si nécessaire
if (!file_exists(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// Fonction pour charger les commandes
function loadOrders() {
    if (!file_exists(ORDERS_FILE)) {
        return [];
    }
    $json = file_get_contents(ORDERS_FILE);
    return json_decode($json, true) ?: [];
}

// Fonction pour sauvegarder les commandes
function saveOrders($orders) {
    file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
}

// Générer un ID unique
function generateOrderId() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

// API Endpoints
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST':
        // Créer une nouvelle commande
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['nom']) || !isset($input['email']) || !isset($input['produit'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            exit;
        }
        
        $orders = loadOrders();
        
        $order = [
            'id' => generateOrderId(),
            'nom' => $input['nom'],
            'email' => $input['email'],
            'telephone' => $input['telephone'] ?? '',
            'adresse' => $input['adresse'] ?? '',
            'produit' => $input['produit'],
            'prix' => $input['prix'] ?? 'Sur devis',
            'message' => $input['message'] ?? '',
            'statut' => 'En attente de paiement',
            'date_creation' => date('Y-m-d H:i:s'),
            'paiement_recu' => false
        ];
        
        $orders[] = $order;
        saveOrders($orders);
        
        // Envoyer email de confirmation
        $subject = "Nouvelle commande AsArt'sDev - " . $order['id'];
        $message = "Nouvelle commande reçue:\n\n";
        $message .= "ID: {$order['id']}\n";
        $message .= "Client: {$order['nom']}\n";
        $message .= "Email: {$order['email']}\n";
        $message .= "Produit: {$order['produit']}\n";
        $message .= "Prix: {$order['prix']}\n\n";
        $message .= "Modalités de paiement:\n";
        $message .= "- Virement bancaire (coordonnées à envoyer)\n";
        $message .= "- Chèque à: Asmir Milianni, Rue Léon Bourgeois, Paris\n";
        
        mail(ADMIN_EMAIL, $subject, $message, "From: noreply@asartsdev.com");
        
        // Réponse client
        echo json_encode([
            'success' => true,
            'order' => $order,
            'message' => 'Commande enregistrée. Vous recevrez les coordonnées de paiement par email.'
        ]);
        break;
        
    case 'GET':
        // Récupérer les commandes (protégé par clé API)
        $apiKey = $_GET['api_key'] ?? '';
        
        if ($apiKey !== 'ASARTSDEV_SECRET_2026') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            exit;
        }
        
        $orders = loadOrders();
        echo json_encode(['orders' => $orders]);
        break;
        
    case 'PUT':
        // Mettre à jour une commande (paiement reçu, etc.)
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['id']) || !isset($input['api_key'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Données manquantes']);
            exit;
        }
        
        if ($input['api_key'] !== 'ASARTSDEV_SECRET_2026') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            exit;
        }
        
        $orders = loadOrders();
        $found = false;
        
        foreach ($orders as &$order) {
            if ($order['id'] === $input['id']) {
                if (isset($input['statut'])) $order['statut'] = $input['statut'];
                if (isset($input['paiement_recu'])) $order['paiement_recu'] = $input['paiement_recu'];
                $order['date_mise_a_jour'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            http_response_code(404);
            echo json_encode(['error' => 'Commande non trouvée']);
            exit;
        }
        
        saveOrders($orders);
        echo json_encode(['success' => true, 'message' => 'Commande mise à jour']);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
}
