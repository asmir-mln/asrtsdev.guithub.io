#!/php
/* 
 * AsArt'sDev | EXEMPLES D'UTILISATION | Base de Données Clients
 * =================================================================
 * 
 * Ce fichier contient des exemples pratiques d'intégration
 * de la base de données clients Symfony
 * 
 * Exécution: php api/exemples.php
 */

// ===============================
// 1️⃣ INITIALISATION SYMFONY
// ===============================

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use App\Entity\Client;
use App\Service\ClientService;

// Bootstrap Symfony (à adapter à votre config)
// $kernel = new AppKernel('prod', false);
// $container = $kernel->getContainer();

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║  AsArt'sDev - Exemples Base de Données Clients            ║\n";
echo "║  Symfony + Doctrine ORM                                   ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// ===============================
// 2️⃣ CRÉER UN CLIENT
// ===============================

echo "➤ EXEMPLE 1: Créer un nouveau client\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
$client = new Client();
$client->setNom('Milianni');
$client->setPrenom('Asmir');
$client->setEmail('asmir@asartsdev.com');
$client->setTelephone('0781586882');
$client->setVille('Paris');
$client->setPays('France');
$client->setEntreprise('AsArt\'sDev');
$client->setFonction('Fondateur & CTO');
$client->setTypeClient('partenaire');
$client->setStatut('actif');
$client->setInterets(['IA', 'Innovation', 'Tech Inclusive']);
$client->setNotes('Client VIP - Partenaire stratégique');

$entityManager->persist($client);
$entityManager->flush();

echo "✅ Client créé avec ID: " . $client->getId();
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 3️⃣ METTRE À JOUR UN CLIENT
// ===============================

echo "➤ EXEMPLE 2: Mettre à jour un client\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
$client = $entityManager->getRepository(Client::class)->find(1);

$client->setStatut('actif');
$client->setDateDernierContact(new DateTime());
$client->setInterets(['IA', 'Innovation', 'Santé', 'Accessibilité']);

$entityManager->flush();

echo "✅ Client mis à jour";
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 4️⃣ UPLOADER UN DOCUMENT
// ===============================

echo "➤ EXEMPLE 3: Uploader un document\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
// Depuis un contrôleur Symfony
public function upload(Request $request, Client $client, ClientService $clientService)
{
    $uploadedFile = $request->files->get('cv');
    
    try {
        $document = $clientService->handleDocumentUpload(
            $client,
            $uploadedFile,
            'cv'
        );
        
        return new JsonResponse([
            'success' => true,
            'documentId' => $document->getId(),
            'message' => 'Document uploadé: ' . $document->getNomFichier()
        ]);
    } catch (Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 400);
    }
}
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 5️⃣ RECHERCHER DES CLIENTS
// ===============================

echo "➤ EXEMPLE 4: Rechercher des clients\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
$clientRepo = $entityManager->getRepository(Client::class);

// Tous les clients actifs
$actifs = $clientRepo->findActifs();

// Clients de type "entreprise"
$entreprises = $clientRepo->findByType('entreprise');

// Clients enregistrés ces 7 derniers jours
$recents = $clientRepo->findRecent();

// Recherche par mot-clé
$results = $clientRepo->search('Asmir');

// Clients avec documents
$withDocs = $clientRepo->findWithDocuments();

// Statistiques
$stats = $clientRepo->getStatistiques();
echo "Total: " . $stats['totalClients'] . " | Actifs: " . $stats['clientsActifs'];
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 6️⃣ CRÉER UNE COMMANDE
// ===============================

echo "➤ EXEMPLE 5: Créer une commande\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
use App\Entity\Commande;

$client = $entityManager->getRepository(Client::class)->find(1);

$commande = new Commande();
$commande->setClient($client);
$commande->setNumeroCommande('CMD-2026-001');
$commande->setTypeProjet('partenariat');
$commande->setDescription('Partenariat innovation IA inclusive');
$commande->setMontant(50000.00);
$commande->setAcompte(15000.00);
$commande->setStatut('confirmee');
$commande->setDateLivraisonPrevue(new DateTime('2026-06-09'));

$entityManager->persist($commande);
$entityManager->flush();

echo "✅ Commande créée: " . $commande->getNumeroCommande();
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 7️⃣ UTILISER LE SERVICE CLIENT
// ===============================

echo "➤ EXEMPLE 6: Service ClientService\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
// Dans un contrôleur
public function monAction(ClientService $clientService)
{
    $client = $entityManager->getRepository(Client::class)->find(1);
    
    // Obtenir les stats
    $stats = $clientService->getClientStats($client);
    echo "Commandes: " . $stats['totalCommandes'];
    echo "Montant total: " . $stats['montantTotal'] . "€";
    
    // Envoyer un email
    $clientService->sendConfirmationEmail($client);
    
    // Générer un rapport
    $rapport = $clientService->generateClientReport($client);
}
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 8️⃣ PAGINATION & FILTRAGE
// ===============================

echo "➤ EXEMPLE 7: Pagination avec QueryBuilder\n";
echo "───────────────────────────────────────────\n";

$exemplCode = <<<'PHP'
$page = 1;
$perPage = 10;

$query = $entityManager->getRepository(Client::class)
    ->createQueryBuilder('c')
    ->where('c.statut = :statut')
    ->setParameter('statut', 'actif')
    ->orderBy('c.dateInscription', 'DESC')
    ->setMaxResults($perPage)
    ->setFirstResult(($page - 1) * $perPage);

$clients = $query->getQuery()->getResult();
$total = $entityManager->getRepository(Client::class)->count(['statut' => 'actif']);

echo "Page " . $page . "/" . ceil($total / $perPage);
echo "Affichage: " . count($clients) . " clients";
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 9️⃣ TRANSACTIONS & ROLLBACK
// ===============================

echo "➤ EXEMPLE 8: Transactions\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
try {
    $entityManager->beginTransaction();
    
    // Créer client
    $client = new Client();
    $client->setNom('Test');
    $client->setEmail(time() . '@test.com');
    
    // Créer commande
    $commande = new Commande();
    $commande->setClient($client);
    $commande->setTypeProjet('livre');
    
    $entityManager->persist($client);
    $entityManager->persist($commande);
    $entityManager->flush();
    
    $entityManager->commit();
    echo "✅ Transaction validée";
} catch (Exception $e) {
    $entityManager->rollback();
    echo "❌ Erreur: " . $e->getMessage();
}
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 🔟 SUPPRIMER UN CLIENT
// ===============================

echo "➤ EXEMPLE 9: Supprimer un client\n";
echo "───────────────────────────────────────\n";

$exemplCode = <<<'PHP'
$client = $entityManager->getRepository(Client::class)->find(999);

if ($client) {
    // Les documents et commandes seront supprimés en cascade
    $entityManager->remove($client);
    $entityManager->flush();
    echo "✅ Client supprimé (cascade: documents, commandes)";
} else {
    echo "❌ Client introuvable";
}
PHP;

echo $exemplCode . "\n\n";

// ===============================
// 🎯 RÉSUMÉ DES ROUTES API
// ===============================

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           ROUTES API DISPONIBLES                          ║\n";
echo "╠════════════════════════════════════════════════════════════╣\n";

$routes = [
    'POST   /clients/enregistrement' => 'Créer un nouveau client',
    'GET    /clients/list' => 'Lister les clients avec filtrage',
    'GET    /clients/{id}/dashboard' => 'Dashboard client',
    'POST   /clients/{id}/edit' => 'Mettre à jour le profil',
    'POST   /clients/{id}/upload' => 'Uploader un document',
];

foreach ($routes as $route => $description) {
    echo "║ " . str_pad($route, 32) . " → " . $description . "\n";
}

echo "╠════════════════════════════════════════════════════════════╣\n";
echo "║           BASE DE DONNÉES                                 ║\n";
echo "╠════════════════════════════════════════════════════════════╣\n";

$tables = [
    'clients' => '5 colonnes principales + métadonnées',
    'commandes' => 'Projets liés aux clients',
    'documents' => 'Uploads avec métadonnées',
    'activites_clients' => 'Log de toutes les actions',
];

foreach ($tables as $table => $desc) {
    echo "║ " . str_pad($table, 25) . " → " . $desc . "\n";
}

echo "╚════════════════════════════════════════════════════════════╝\n\n";

echo "✅ Exemples compilés avec succès\n";
echo "📖 Pour plus d'info, consultez CLIENT_DATABASE_README.md\n";
