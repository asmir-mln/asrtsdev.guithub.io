<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Entity\BandeAudio;
use App\Entity\CommentaireAudio;
use App\Service\LivreService;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/livres")
 */
class LivreController extends AbstractController
{
    private $entityManager;
    private $livreService;
    private $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LivreService $livreService,
        NotificationService $notificationService
    ) {
        $this->entityManager = $entityManager;
        $this->livreService = $livreService;
        $this->notificationService = $notificationService;
    }

    /**
     * Liste tous les livres avec leurs statistiques audio
     * 
     * @Route("/with-audio", name="livres_avec_audio", methods={"GET"})
     */
    public function getLivresAvecAudio(): JsonResponse
    {
        try {
            $livres = $this->entityManager
                ->getRepository(Livre::class)
                ->findBy([], ['numero_livre' => 'ASC']);

            $data = [];
            foreach ($livres as $livre) {
                $data[] = [
                    'id' => $livre->getId(),
                    'titre' => $livre->getTitre(),
                    'description' => $livre->getDescription(),
                    'numero_livre' => $livre->getNumeroLivre(),
                    'isbn' => $livre->getIsbn(),
                    'prix' => $livre->getPrix(),
                    'nombre_pages' => $livre->getNombrePages(),
                    'poids_kg' => $livre->getPoidsKg(),
                    'disponible' => $livre->isDisponible(),
                    'stock_disponible' => $livre->getStockDisponible(),
                    'image_couverture' => $livre->getImageCouverture(),
                    'url_preview' => $livre->getUrlPreview(),
                    'statistiques_audio' => [
                        'nombre_bandes_audio' => count($livre->getBandesAudio()),
                        'nombre_commentaires' => count($livre->getCommentairesAudio()),
                        'duree_totale_lecture' => $livre->getDureeTotaleAudio(),
                    ],
                    'bandes_audio' => $this->serializeBandesAudio($livre->getBandesAudio()),
                    'commentaires_audio' => $this->serializeCommentairesAudio($livre->getCommentairesAudio()),
                ];
            }

            return new JsonResponse([
                'success' => true,
                'livres' => $data,
                'total' => count($data)
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur lors du chargement des livres',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Détails d'un livre spécifique
     * 
     * @Route("/{id}", name="livre_details", methods={"GET"})
     */
    public function getLivreDetails(int $id): JsonResponse
    {
        try {
            $livre = $this->entityManager
                ->getRepository(Livre::class)
                ->find($id);

            if (!$livre) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'livre' => [
                    'id' => $livre->getId(),
                    'titre' => $livre->getTitre(),
                    'description' => $livre->getDescription(),
                    'numero_livre' => $livre->getNumeroLivre(),
                    'isbn' => $livre->getIsbn(),
                    'prix' => $livre->getPrix(),
                    'nombre_pages' => $livre->getNombrePages(),
                    'poids_kg' => $livre->getPoidsKg(),
                    'disponible' => $livre->isDisponible(),
                    'stock_disponible' => $livre->getStockDisponible(),
                    'bandes_audio' => $this->serializeBandesAudio($livre->getBandesAudio()),
                    'commentaires_audio' => $this->serializeCommentairesAudio($livre->getCommentairesAudio()),
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Enregistrer une écoute de bande audio
     * 
     * @Route("/audio/{id}/ecoute", name="bande_audio_ecoute", methods={"POST"})
     */
    public function enregistrerEcouteBandeAudio(int $id): JsonResponse
    {
        try {
            $bandeAudio = $this->entityManager
                ->getRepository(BandeAudio::class)
                ->find($id);

            if (!$bandeAudio) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Bande audio non trouvée'
                ], Response::HTTP_NOT_FOUND);
            }

            // Incrémenter le compteur d'écoutes
            $bandeAudio->incrementerEcoutes();
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Écoute enregistrée',
                'total_ecoutes' => $bandeAudio->getNombreEcoutes()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Enregistrer une écoute de commentaire audio
     * 
     * @Route("/commentaire/{id}/ecoute", name="commentaire_audio_ecoute", methods={"POST"})
     */
    public function enregistrerEcouteCommentaire(int $id): JsonResponse
    {
        try {
            $commentaire = $this->entityManager
                ->getRepository(CommentaireAudio::class)
                ->find($id);

            if (!$commentaire) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Commentaire audio non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            // Incrémenter le compteur d'écoutes
            $commentaire->incrementerEcoutes();
            $this->entityManager->flush();

            return new JsonResponse([
                'success' => true,
                'message' => 'Écoute enregistrée',
                'total_ecoutes' => $commentaire->getNombreEcoutes()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Commander un livre avec options de livraison
     * 
     * @Route("/commander", name="commander_livre", methods={"POST"})
     */
    public function commanderLivre(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation
            if (!isset($data['livre_id'])) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'ID du livre manquant'
                ], Response::HTTP_BAD_REQUEST);
            }

            $livre = $this->entityManager
                ->getRepository(Livre::class)
                ->find($data['livre_id']);

            if (!$livre) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            if (!$livre->isDisponible() || $livre->getStockDisponible() < 1) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Livre non disponible en stock'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Options de livraison
            $options = [
                'livre' => $livre,
                'type_envoi' => $data['type_envoi'] ?? 'standard',
                'dedicace_demandee' => $data['dedicace_demandee'] ?? false,
                'dedicace_texte' => $data['dedicace_texte'] ?? null,
                'adresse_livraison' => $data['adresse_livraison'] ?? null,
                'client_email' => $data['email'] ?? null,
                'client_nom' => $data['nom'] ?? null,
            ];

            // Estimer le prix de livraison
            $estimationLivraison = $this->livreService->estimerPrixLivraison($options);

            // Créer la commande (intégration avec système existant)
            $commande = $this->livreService->creerCommandeLivre($options);

            // Envoyer notification automatique
            $this->notificationService->envoyerConfirmationCommandeLivre($commande);

            return new JsonResponse([
                'success' => true,
                'message' => 'Commande créée avec succès',
                'commande' => [
                    'numero_commande' => $commande->getNumeroCommande(),
                    'montant_livre' => $livre->getPrix(),
                    'montant_livraison' => $estimationLivraison['prix_total'],
                    'montant_total' => $livre->getPrix() + $estimationLivraison['prix_total'],
                    'delai_estime' => $estimationLivraison['delai_livraison'] . ' jours',
                    'type_envoi' => $options['type_envoi'],
                    'dedicace' => $options['dedicace_demandee']
                ]
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la création de la commande',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Demander une dédicace personnalisée
     * 
     * @Route("/demande-dedicace", name="demande_dedicace", methods={"POST"})
     */
    public function demanderDedicace(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validation
            $required = ['livre_id', 'nom', 'email', 'message_dedicace'];
            foreach ($required as $field) {
                if (!isset($data[$field])) {
                    return new JsonResponse([
                        'success' => false,
                        'error' => "Le champ {$field} est obligatoire"
                    ], Response::HTTP_BAD_REQUEST);
                }
            }

            $livre = $this->entityManager
                ->getRepository(Livre::class)
                ->find($data['livre_id']);

            if (!$livre) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Livre non trouvé'
                ], Response::HTTP_NOT_FOUND);
            }

            // Envoyer email à l'auteur pour demande de dédicace
            $this->notificationService->envoyerDemandeDedicace([
                'livre' => $livre,
                'client_nom' => $data['nom'],
                'client_email' => $data['email'],
                'message_dedicace' => $data['message_dedicace'],
                'adresse_envoi' => $data['adresse'] ?? 'À déterminer'
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Demande de dédicace envoyée ! Vous recevrez une confirmation sous 24-48h.',
                'prochaines_etapes' => [
                    '1. Validation de votre demande par l\'auteur',
                    '2. Création de votre dédicace personnalisée',
                    '3. Envoi du livre avec suivi',
                    '4. Livraison estimée: 3-5 jours'
                ],
                'cout_supplementaire' => 30.00,
                'delai_estime' => '3-5 jours ouvrés'
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Méthodes privées de sérialisation

    private function serializeBandesAudio($bandesAudio): array
    {
        $result = [];
        foreach ($bandesAudio as $bande) {
            $result[] = [
                'id' => $bande->getId(),
                'titre' => $bande->getTitre(),
                'description' => $bande->getDescription(),
                'fichier_audio' => $bande->getFichierAudio(),
                'format' => $bande->getFormat(),
                'duree_secondes' => $bande->getDureeSecondes(),
                'duree_formatee' => $bande->getDureeFormatee(),
                'ordre' => $bande->getOrdre(),
                'type' => $bande->getType(),
                'acces_gratuit' => $bande->isAccesGratuit(),
                'nombre_ecoutes' => $bande->getNombreEcoutes()
            ];
        }
        return $result;
    }

    private function serializeCommentairesAudio($commentaires): array
    {
        $result = [];
        foreach ($commentaires as $commentaire) {
            $result[] = [
                'id' => $commentaire->getId(),
                'titre' => $commentaire->getTitre(),
                'description' => $commentaire->getDescription(),
                'fichier_audio' => $commentaire->getFichierAudio(),
                'duree_secondes' => $commentaire->getDureeSecondes(),
                'duree_formatee' => $commentaire->getDureeFormatee(),
                'auteur' => $commentaire->getAuteurCommentaire(),
                'chapitre_reference' => $commentaire->getChapitreReference(),
                'page_reference' => $commentaire->getPageReference(),
                'reference_complete' => $commentaire->getReferenceComplete(),
                'type_commentaire' => $commentaire->getTypeCommentaire(),
                'acces_premium' => $commentaire->isAccesPremium(),
                'nombre_ecoutes' => $commentaire->getNombreEcoutes(),
                'transcription' => $commentaire->getTranscription()
            ];
        }
        return $result;
    }
}
