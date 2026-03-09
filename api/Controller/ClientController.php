<?php
/* AsArt'sDev | ClientController | Symfony HTTP | Signature invisible | ASmir Milia */

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Document;
use App\Entity\ActiviteClient;
use App\Form\ClientFormType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse, File\UploadedFile};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/clients", name="client_")
 */
class ClientController extends AbstractController
{
    private $em;
    private $clientRepository;

    public function __construct(EntityManagerInterface $em, ClientRepository $clientRepository)
    {
        $this->em = $em;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/enregistrement", name="register", methods={"GET","POST"})
     */
    public function register(Request $request, SluggerInterface $slugger): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter les intérêts (string -> array)
            if ($interets = $form->get('interets')->getData()) {
                $client->setInterets(array_map('trim', explode(',', $interets)));
            }

            // Sauvegarder le client
            $this->em->persist($client);
            $this->em->flush();

            // Log l'activité
            $this->logActivite($client, 'creation', 'Nouveau client enregistré');

            // Créer le dossier documents du client
            $this->creerDossierDocuments($client, $slugger);

            $this->addFlash('success', '✅ Enregistrement réussi! Bienvenue ' . $client->getNomComplet());
            return $this->redirectToRoute('client_dashboard', ['id' => $client->getId()]);
        }

        return $this->render('client/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(Client $client): Response
    {
        return $this->render('client/dashboard.html.twig', [
            'client' => $client,
            'commandes' => $client->getCommandes(),
            'documents' => $client->getDocuments(),
            'activites' => $client->getActivites(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Client $client): Response
    {
        $form = $this->createForm(ClientFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traiter les intérêts
            if ($interets = $form->get('interets')->getData()) {
                $client->setInterets(array_map('trim', explode(',', $interets)));
            }

            $client->setDateModification(new \DateTime());
            $this->em->flush();

            $this->logActivite($client, 'modification', 'Profil mis à jour');
            $this->addFlash('success', '✅ Profil mis à jour avec succès');
            return $this->redirectToRoute('client_dashboard', ['id' => $client->getId()]);
        }

        // Rejoindre les intérêts pour l'affichage
        $form->get('interets')->setData(implode(', ', $client->getInterets() ?? []));

        return $this->render('client/edit.html.twig', [
            'form' => $form->createView(),
            'client' => $client,
        ]);
    }

    /**
     * @Route("/{id}/upload", name="upload_document", methods={"POST"})
     */
    public function uploadDocument(Request $request, Client $client, SluggerInterface $slugger): JsonResponse
    {
        $uploadedFile = $request->files->get('document');
        
        if (!$uploadedFile) {
            return new JsonResponse(['error' => 'Aucun fichier fourni'], 400);
        }

        // Valider le fichier
        $maxSize = 50 * 1024 * 1024; // 50MB
        if ($uploadedFile->getSize() > $maxSize) {
            return new JsonResponse(['error' => 'Fichier trop volumineux'], 413);
        }

        try {
            // Générer le nom du fichier
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            // Déplacer le fichier
            $uploadDir = '/documents/clients/' . $client->getId();
            $uploadedFile->move($uploadDir, $newFilename);

            // Créer l'entité Document
            $document = new Document();
            $document->setClient($client);
            $document->setNomFichier($uploadedFile->getClientOriginalName());
            $document->setPathFichier($uploadDir . '/' . $newFilename);
            $document->setMimeType($uploadedFile->getMimeType());
            $document->setTailleBytes($uploadedFile->getSize());
            $document->setTypeDocument($request->get('type', 'autre'));

            $this->em->persist($document);
            $this->em->flush();

            $this->logActivite($client, 'document_upload', 'Document uploadé: ' . $uploadedFile->getClientOriginalName());

            return new JsonResponse([
                'success' => true,
                'message' => 'Document uploadé avec succès',
                'document' => [
                    'id' => $document->getId(),
                    'nom' => $document->getNomFichier(),
                    'type' => $document->getTypeDocument(),
                    'date' => $document->getDateUpload()->format('d/m/Y H:i'),
                ]
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/{id}/list", name="list", methods={"GET"})
     */
    public function listClients(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $statut = $request->query->get('statut');
        $type = $request->query->get('type');

        $query = $this->clientRepository->createQueryBuilder('c');

        if ($statut) {
            $query->andWhere('c.statut = :statut')->setParameter('statut', $statut);
        }

        if ($type) {
            $query->andWhere('c.typeClient = :type')->setParameter('type', $type);
        }

        $query->orderBy('c.dateInscription', 'DESC')
              ->setMaxResults(20)
              ->setFirstResult(($page - 1) * 20);

        $clients = $query->getQuery()->getResult();
        $total = $this->clientRepository->count([]);

        return $this->render('client/list.html.twig', [
            'clients' => $clients,
            'page' => $page,
            'total' => $total,
            'perPage' => 20,
        ]);
    }

    /**
     * Enregistrer une activité client
     */
    private function logActivite(Client $client, string $type, string $description): void
    {
        $activite = new ActiviteClient();
        $activite->setClient($client);
        $activite->setTypeActivite($type);
        $activite->setDescription($description);
        $this->em->persist($activite);
        $this->em->flush();
    }

    /**
     * Créer le dossier de documents du client
     */
    private function creerDossierDocuments(Client $client, SluggerInterface $slugger): void
    {
        $dossier = '/documents/clients/' . $client->getId();
        if (!file_exists($dossier)) {
            mkdir($dossier, 0755, true);
        }
        $client->setDocumentsPath($dossier);
        $this->em->flush();
    }
}
