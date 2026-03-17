<?php
/**
 * AsArt'sDev Orders API
 * Gestion des commandes avec enregistrement DB + fallback JSON
 * Open-source - MIT License
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuration
define('ORDERS_FILE', __DIR__ . '/data/orders.json');
define('ADMIN_EMAIL', 'asartdev.contact@gmail.com');
define('API_KEY', 'ASARTSDEV_SECRET_2026');

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

function normalizePrice($value): float {
    if (is_numeric($value)) {
        return (float) $value;
    }
    $clean = str_replace(['€', ' '], '', (string) $value);
    $clean = str_replace(',', '.', $clean);
    return is_numeric($clean) ? (float) $clean : 0.0;
}

function getClientIp(): string {
    foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            $value = explode(',', (string) $_SERVER[$key])[0];
            return trim($value);
        }
    }
    return '0.0.0.0';
}

function getDbConnection(): ?PDO {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'asartsdev';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    } catch (Throwable $e) {
        return null;
    }
}

function ensureOrdersTable(PDO $pdo): void {
    $sql = "
        CREATE TABLE IF NOT EXISTS orders_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id VARCHAR(40) NOT NULL UNIQUE,
            nom VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            telephone VARCHAR(50) NULL,
            adresse TEXT NULL,
            produit VARCHAR(255) NOT NULL,
            prix DECIMAL(10,2) NOT NULL DEFAULT 0,
            quantite INT NOT NULL DEFAULT 1,
            panier_json LONGTEXT NULL,
            subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
            remise DECIMAL(10,2) NOT NULL DEFAULT 0,
            frais_envoi DECIMAL(10,2) NOT NULL DEFAULT 0,
            frais_service DECIMAL(10,2) NOT NULL DEFAULT 0,
            total DECIMAL(10,2) NOT NULL DEFAULT 0,
            message LONGTEXT NULL,
            statut VARCHAR(120) NOT NULL DEFAULT 'En attente de paiement',
            paiement_recu TINYINT(1) NOT NULL DEFAULT 0,
            ip_adresse VARCHAR(45) NULL,
            user_agent LONGTEXT NULL,
            date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            date_mise_a_jour DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_orders_email (email),
            INDEX idx_orders_statut (statut),
            INDEX idx_orders_date (date_creation)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    $pdo->exec($sql);
}

function saveOrderToDb(PDO $pdo, array $order): void {
    ensureOrdersTable($pdo);

    $stmt = $pdo->prepare("INSERT INTO orders_records
        (order_id, nom, email, telephone, adresse, produit, prix, quantite, panier_json, subtotal, remise, frais_envoi, frais_service, total, message, statut, paiement_recu, ip_adresse, user_agent)
        VALUES
        (:order_id, :nom, :email, :telephone, :adresse, :produit, :prix, :quantite, :panier_json, :subtotal, :remise, :frais_envoi, :frais_service, :total, :message, :statut, :paiement_recu, :ip_adresse, :user_agent)");

    $stmt->execute([
        ':order_id' => $order['id'],
        ':nom' => $order['nom'],
        ':email' => $order['email'],
        ':telephone' => $order['telephone'],
        ':adresse' => $order['adresse'],
        ':produit' => $order['produit'],
        ':prix' => $order['prix_numeric'],
        ':quantite' => $order['quantite'],
        ':panier_json' => $order['panier_json'],
        ':subtotal' => $order['subtotal'],
        ':remise' => $order['remise'],
        ':frais_envoi' => $order['frais_envoi'],
        ':frais_service' => $order['frais_service'],
        ':total' => $order['total'],
        ':message' => $order['message'],
        ':statut' => $order['statut'],
        ':paiement_recu' => $order['paiement_recu'] ? 1 : 0,
        ':ip_adresse' => $order['ip_adresse'],
        ':user_agent' => $order['user_agent'],
    ]);
}

function loadOrdersFromDb(PDO $pdo): array {
    ensureOrdersTable($pdo);
    $stmt = $pdo->query("SELECT * FROM orders_records ORDER BY date_creation DESC");
    $rows = $stmt->fetchAll();

    return array_map(function ($row) {
        return [
            'id' => $row['order_id'],
            'nom' => $row['nom'],
            'email' => $row['email'],
            'telephone' => $row['telephone'],
            'adresse' => $row['adresse'],
            'produit' => $row['produit'],
            'prix' => number_format((float) $row['prix'], 2, ',', ' ') . ' €',
            'quantite' => (int) $row['quantite'],
            'panier' => $row['panier_json'] ? (json_decode($row['panier_json'], true) ?: []) : [],
            'subtotal' => (float) $row['subtotal'],
            'remise' => (float) $row['remise'],
            'frais_envoi' => (float) $row['frais_envoi'],
            'frais_service' => (float) $row['frais_service'],
            'total' => (float) $row['total'],
            'message' => $row['message'],
            'statut' => $row['statut'],
            'date_creation' => $row['date_creation'],
            'paiement_recu' => (bool) $row['paiement_recu'],
        ];
    }, $rows);
}

function updateOrderDb(PDO $pdo, array $input): bool {
    ensureOrdersTable($pdo);
    $fields = [];
    $params = [':order_id' => $input['id']];

    if (isset($input['statut'])) {
        $fields[] = 'statut = :statut';
        $params[':statut'] = $input['statut'];
    }
    if (isset($input['paiement_recu'])) {
        $fields[] = 'paiement_recu = :paiement_recu';
        $params[':paiement_recu'] = $input['paiement_recu'] ? 1 : 0;
    }

    if (!$fields) {
        return false;
    }

    $sql = 'UPDATE orders_records SET ' . implode(', ', $fields) . ', date_mise_a_jour = NOW() WHERE order_id = :order_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount() > 0;
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
        
        $prixNumeric = normalizePrice($input['prix'] ?? 0);
        $quantite = max(1, (int) ($input['quantite'] ?? 1));
        $subtotal = isset($input['subtotal']) ? (float) $input['subtotal'] : ($prixNumeric * $quantite);
        $remise = isset($input['remise']) ? (float) $input['remise'] : round($subtotal * 0.05, 2);
        $fraisEnvoi = isset($input['frais_envoi']) ? (float) $input['frais_envoi'] : 0.0;
        $fraisService = isset($input['frais_service']) ? (float) $input['frais_service'] : 0.0;
        $total = isset($input['total']) ? (float) $input['total'] : ($subtotal - $remise + $fraisEnvoi + $fraisService);

        $orders = loadOrders();
        
        $order = [
            'id' => generateOrderId(),
            'nom' => $input['nom'],
            'email' => $input['email'],
            'telephone' => $input['telephone'] ?? '',
            'adresse' => $input['adresse'] ?? '',
            'produit' => $input['produit'],
            'prix' => $prixNumeric > 0 ? number_format($prixNumeric, 2, ',', ' ') . ' €' : 'Sur devis',
            'prix_numeric' => round($prixNumeric, 2),
            'quantite' => $quantite,
            'panier' => $input['panier'] ?? [],
            'panier_json' => json_encode($input['panier'] ?? [], JSON_UNESCAPED_UNICODE),
            'subtotal' => round($subtotal, 2),
            'remise' => round($remise, 2),
            'frais_envoi' => round($fraisEnvoi, 2),
            'frais_service' => round($fraisService, 2),
            'total' => round($total, 2),
            'message' => $input['message'] ?? '',
            'statut' => 'En attente de paiement',
            'date_creation' => date('Y-m-d H:i:s'),
            'paiement_recu' => false,
            'ip_adresse' => getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];

        // Enregistrement DB si disponible, sinon fallback JSON.
        $dbSaved = false;
        $pdo = getDbConnection();
        if ($pdo) {
            try {
                saveOrderToDb($pdo, $order);
                $dbSaved = true;
            } catch (Throwable $e) {
                $dbSaved = false;
            }
        }

        if (!$dbSaved) {
            $fallbackOrder = $order;
            unset($fallbackOrder['prix_numeric'], $fallbackOrder['panier_json'], $fallbackOrder['user_agent']);
            $orders[] = $fallbackOrder;
            saveOrders($orders);
        }

        // Envoyer email de confirmation
        $subject = "Nouvelle commande AsArt'sDev - " . $order['id'];
        $message = "Nouvelle commande reçue:\n\n";
        $message .= "ID: {$order['id']}\n";
        $message .= "Client: {$order['nom']}\n";
        $message .= "Email: {$order['email']}\n";
        $message .= "Produit: {$order['produit']}\n";
        $message .= "Sous-total: {$order['subtotal']} EUR\n";
        $message .= "Remise: {$order['remise']} EUR\n";
        $message .= "Frais envoi: {$order['frais_envoi']} EUR\n";
        $message .= "Frais service: {$order['frais_service']} EUR\n";
        $message .= "Total facture: {$order['total']} EUR\n\n";
        $message .= "Modalités de paiement:\n";
        $message .= "- Virement bancaire (coordonnées à envoyer)\n";
        $message .= "- Chèque à: Asmir Milianni, Rue Léon Bourgeois, Paris\n";
        
        mail(ADMIN_EMAIL, $subject, $message, "From: noreply@asartsdev.com");
        @mail($order['email'], "Confirmation prise de commande - {$order['id']}", "Bonjour {$order['nom']},\n\nVotre commande est bien enregistree.\nReference: {$order['id']}\nTotal facture: {$order['total']} EUR\n\nMerci pour votre confiance.\nAsArt'sDev", "From: noreply@asartsdev.com");
        
        // Réponse client
        echo json_encode([
            'success' => true,
            'order' => $order,
            'message' => 'Commande enregistree instantanement. Confirmation envoyee.',
            'db_saved' => $dbSaved
        ]);
        break;
        
    case 'GET':
        // Récupérer les commandes (protégé par clé API)
        $apiKey = $_GET['api_key'] ?? '';
        
        if ($apiKey !== API_KEY) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            exit;
        }

        $pdo = getDbConnection();
        if ($pdo) {
            try {
                $orders = loadOrdersFromDb($pdo);
            } catch (Throwable $e) {
                $orders = loadOrders();
            }
        } else {
            $orders = loadOrders();
        }
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
        
        if ($input['api_key'] !== API_KEY) {
            http_response_code(403);
            echo json_encode(['error' => 'Accès non autorisé']);
            exit;
        }

        $found = false;
        $pdo = getDbConnection();
        if ($pdo) {
            try {
                $found = updateOrderDb($pdo, $input);
            } catch (Throwable $e) {
                $found = false;
            }
        }

        if (!$found) {
            $orders = loadOrders();
            foreach ($orders as &$order) {
                if ($order['id'] === $input['id']) {
                    if (isset($input['statut'])) $order['statut'] = $input['statut'];
                    if (isset($input['paiement_recu'])) $order['paiement_recu'] = $input['paiement_recu'];
                    $order['date_mise_a_jour'] = date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }

            if ($found) {
                saveOrders($orders);
            }
        }

        if (!$found) {
            http_response_code(404);
            echo json_encode(['error' => 'Commande non trouvée']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Commande mise à jour']);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
}
