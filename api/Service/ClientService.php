|||||||||||||||||||||||||||||``````````````````````````````````````````````````````````````````yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy<?php
/* AsArt'sDev | ClientService | Document Management | Signature invisible | ASmir Milia */

namespace App\Service;

use App\Entity\Client;
use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ClientService
{
    private $em;
    private $params;
    private $slugger;

    public function __construct(
        EntityManagerInterface $em,
        ParameterBagInterface $params,
        SluggerInterface $slugger
    ) {
        $this->em = $em;
        $this->params = $params;
        $this->slugger = $slugger;
    }

    /**
     * Traiter l'upload d'un document
     */
    public function handleDocumentUpload(Client $client, UploadedFile $file, string $typeDocument = 'autre'): Document
    {
        // Validation
        $maxSize = 50 * 1024 * 1024; // 50MB
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'text/plain'];

        if ($file->getSize() > $maxSize) {
            throw new \Exception('Fichier trop volumineux (max 50MB)');
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \Exception('Type de fichier non autorisé');
        }

        // Générer le nom
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalName);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        // Créer le dossier
        $uploadDir = 'documents/clients/' . $client->getId();
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Déplacer le fichier
        $file->move($uploadDir, $newFilename);

        // Créer l'entité
        $document = new Document();
        $document->setClient($client);
        $document->setNomFichier($file->getClientOriginalName());
        $document->setPathFichier($uploadDir . '/' . $newFilename);
        $document->setMimeType($file->getMimeType());
        $document->setTailleBytes($file->getSize());
        $document->setTypeDocument($typeDocument);

        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /**
     * Supprimer un document
     */
    public function deleteDocument(Document $document): void
    {
        // Supprimer le fichier physique
        if (file_exists($document->getPathFichier())) {
            unlink($document->getPathFichier());
        }

        // Supprimer de la BD
        $this->em->remove($document);
        $this->em->flush();
    }

    /**
     * Obtenir les statistiques d'un client
     */
    public function getClientStats(Client $client): array
    {
        return [
            'totalCommandes' => $client->getCommandes()->count(),
            'totalDocuments' => $client->getDocuments()->count(),
            'montantTotal' => array_sum($client->getCommandes()->map(function ($c) {
                return $c->getMontant() ?? 0;
            })->toArray()),
            'derniereActivite' => $client->getActivites()->first()?->getDateActivite(),
        ];
    }

    /**
     * Envoyer un email de confirmation
     */
    public function sendConfirmationEmail(Client $client): void
    {
        // TODO: Implémenter avec Mailer Symfony
        // Pour le moment, log simple
        error_log("Email de confirmation envoyé à: " . $client->getEmail());
    }

    /**
     * Générer un rapport client PDF
     */
    public function generateClientReport(Client $client): string
    {
        // TODO: Utiliser Dompdf ou autre librairie
        $content = "=== RAPPORT CLIENT ===\n\n";
        $content .= "Nom: " . $client->getNomComplet() . "\n";
        $content .= "Email: " . $client->getEmail() . "\n";
        $content .= "Type: " . $client->getTypeClient() . "\n";
        $content .= "Statut: " . $client->getStatut() . "\n";
        $content .= "Date inscription: " . $client->getDateInscription()->format('d/m/Y') . "\n\n";
        $content .= "Commandes: " . $client->getCommandes()->count() . "\n";
        $content .= "Documents: " . $client->getDocuments()->count() . "\n";

        return $content;
    }
}
