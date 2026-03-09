<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Commande;
use App\Entity\Donation;
use App\Form\DonationFormType;
use App\Service\ReminderService;

/**
 * @Route("/donation", name="donation_")
 */
class DonationController extends AbstractController
{
    private $em;
    private $reminderService;

    public function __construct(EntityManagerInterface $em, ReminderService $reminderService)
    {
        $this->em = $em;
        $this->reminderService = $reminderService;
    }

    /**
     * 💰 Formulaire de donation
     * 
     * @Route("/formulaire", name="form", methods={"GET"})
     */
    public function formulaire(): Response
    {
        $form = $this->createForm(DonationFormType::class);

        return $this->render('donation/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * 💳 Traiter la donation
     * 
     * @Route("/submit", name="submit", methods={"POST"})
     */
    public function submit(Request $request): Response
    {
        $form = $this->createForm(DonationFormType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $this->getFormErrors($form)
            ], 400);
        }

        try {
            $data = $form->getData();

            // Récupérer l'IP du client
            $ip = $request->getClientIp();
            $userAgent = $request->headers->get('User-Agent');

            // ========================================
            // 1. Créer la commande
            // ========================================

            $commande = new Commande();
            $commande->setNumeroCommande('DON-' . date('mds') . '-' . substr(bin2hex(random_bytes(2)), 0, 4));
            $commande->setTypeProjet('donation');
            $commande->setMontant($data['montant']);
            $commande->setStatut('en_attente_paiement');
            $commande->setTypeDonation($data['typeDonation']);
            $commande->setIpDonateur($ip);
            $commande->setDescription(
                'Donation ' . $data['montant'] . '€ - ' .
                ($data['typeDonation'] === 'complete' ? $data['nomDonateur'] : 'ANONYME')
            );

            // ========================================
            // 2. Créer la donation
            // ========================================

            $donation = new Donation();
            $donation->setCommande($commande);
            $donation->setMontant($data['montant']);
            $donation->setIpAdresse($ip);
            $donation->setUserAgent($userAgent);

            // Gérer le type de donation
            if ($data['typeDonation'] === 'complete') {
                // Donation complète - collecte les infos
                $donation->setNomDonateur($data['nomDonateur'] ?? null);
                $donation->setEmailDonateur($data['emailDonateur'] ?? null);
                $donation->setTelephoneDonateur($data['telephoneDonateur'] ?? null);
                $donation->setAdresseDonateur($data['adresseDonateur'] ?? null);
                $donation->setEligibleCadeau(true);
                $donation->setStatutSuivi('en_attente');
                $donation->setCodeSuivi(strtoupper(
                    substr($data['nomDonateur'] ?? 'ANON', 0, 5) . '-' . 
                    substr(bin2hex(random_bytes(6)), 0, 8)
                ));
            } else {
                // Donation anonyme - pas d'infos personnelles
                $donation->setEligibleCadeau(false);
                $donation->setStatutSuivi('en_attente');
                $donation->setCodeSuivi('ANON-' . strtoupper(bin2hex(random_bytes(8))));
                // Les champs nom/email restent NULL
            }

            // ========================================
            // 3. Persistencer
            // ========================================

            $this->em->persist($commande);
            $this->em->persist($donation);

            // ========================================
            // 4. Créer reminder initial
            // ========================================

            $messageReminder = sprintf(
                'Donation %s reçue%s',
                $data['montant'] . '€',
                $data['typeDonation'] === 'complete' 
                    ? ' - ' . $data['nomDonateur'] . ' - Eligible cadeau'
                    : ' - ANONYME (Suivi: ' . $donation->getCodeSuivi() . ')'
            );

            $this->reminderService->createReminderForCommande(
                $commande,
                'creation',
                $messageReminder
            );

            $this->em->flush();

            // ========================================
            // 5. Réponse de succès
            // ========================================

            $response = [
                'success' => true,
                'commande_id' => $commande->getId(),
                'donation_id' => $donation->getId(),
                'code_suivi' => $donation->getCodeSuivi(),
                'montant' => $data['montant'],
                'type_donation' => $data['typeDonation'],
            ];

            if ($data['typeDonation'] === 'complete') {
                $response['message'] = sprintf(
                    'Merci %s! Votre donation de %s€ a été reçue. Vous recevrez un email de confirmation.',
                    $data['nomDonateur'],
                    $data['montant']
                );
                $response['email'] = $data['emailDonateur'];
                $response['url_dashboard'] = $this->generateUrl('admin_track_donation_public', [
                    'codeSuivi' => $donation->getCodeSuivi()
                ]);
            } else {
                $response['message'] = sprintf(
                    'Merci pour votre donation anonyme de %s€! Vous pouvez suivre votre donation avec le code: %s',
                    $data['montant'],
                    $donation->getCodeSuivi()
                );
                $response['url_track'] = $this->generateUrl('admin_track_donation_public', [
                    'codeSuivi' => $donation->getCodeSuivi()
                ]);
            }

            return new JsonResponse($response, 201);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur lors de la création de la donation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Confirmation de donation (page post-soumission)
     * 
     * @Route("/confirmation/{id}", name="confirmation", methods={"GET"})
     */
    public function confirmation(Donation $donation): Response
    {
        return $this->render('donation/confirmation.html.twig', [
            'donation' => $donation,
            'commande' => $donation->getCommande(),
        ]);
    }

    /**
     * 🔍 Vérifier le statut d'une donation
     * 
     * @Route("/status/{codeSuivi}", name="status", methods={"GET"})
     */
    public function status(string $codeSuivi): Response
    {
        $donation = $this->em->getRepository(Donation::class)
            ->findOneBy(['codeSuivi' => $codeSuivi]);

        if (!$donation) {
            return new JsonResponse([
                'error' => 'Code suivi non trouvé'
            ], 404);
        }

        $commande = $donation->getCommande();

        return new JsonResponse([
            'code_suivi' => $donation->getCodeSuivi(),
            'montant' => $donation->getMontant(),
            'anonyme' => $donation->isAnonyme(),
            'statut' => $donation->getStatutSuivi(),
            'date_donation' => $donation->getDateDonation()->format('d/m/Y H:i'),
            'eligible_cadeau' => $donation->isEligibleCadeau(),
            'cadeau_envoye' => $donation->getCadeauEnvoye() ? $donation->getCadeauEnvoye()->format('d/m/Y') : null,
            'commande' => [
                'numero' => $commande->getNumeroCommande(),
                'statut' => $commande->getStatut(),
                'type' => $commande->getTypeProjet(),
            ]
        ]);
    }

    /**
     * 🎁 Obtenir les options de cadeaux disponibles selon le montant
     * 
     * @Route("/cadeaux", name="cadeaux", methods={"GET"})
     */
    public function getCadeaux(Request $request): Response
    {
        $montant = (float) $request->query->get('montant', 0);

        $cadeaux = $this->em->getRepository(Cadeau::class)
            ->createQueryBuilder('c')
            ->where('c.actif = true')
            ->andWhere('c.montantMinimum <= :montant')
            ->setParameter('montant', $montant)
            ->orderBy('c.montantMinimum', 'ASC')
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($cadeaux as $cadeau) {
            $data[] = [
                'id' => $cadeau->getId(),
                'nom' => $cadeau->getNomCadeau(),
                'montant_min' => $cadeau->getMontantMinimum(),
                'description' => $cadeau->getDescription(),
                'disponible' => $cadeau->getQuantiteDisponible() === -1 || 
                              $cadeau->getQuantiteDisponible() > $cadeau->getQuantiteUtilisee()
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * 💢 Aide pour formulaire
     * 
     * @Route("/help", name="help", methods={"GET"})
     */
    public function help(): Response
    {
        return $this->render('donation/help.html.twig', [
            'montants' => [
                5 => '👤 Merci personnel',
                25 => '📚 Bibliothèque numérique',
                50 => '🎨 Œuvre exclusive',
                100 => '💝 Pack VIP',
                500 => '🏆 Reconnaissance',
                5000 => '👑 Partenariat',
            ],
            'faqs' => [
                'Pourquoi pas de cadeau si anonyme?' => 'Nous respectons votre vie privée. Les cadeaux nécessitent une adresse pour être envoyés.',
                'Puis-je changer mon type de donation?' => 'Non, le type est définitif à la création. Créez une nouvelle donation si besoin.',
                'Comment suivre ma donation?' => 'Vous recevrez un code de suivi (email pour complète, affichage pour anonyme).',
                'Y a-t-il des frais?' => 'Non, 100% de votre donation va aux projets AsArt\'sDev.',
            ]
        ]);
    }

    // ==================== PRIVATE HELPERS ====================

    /**
     * Extraire les erreurs du formulaire
     */
    private function getFormErrors($form): array
    {
        $errors = [];
        foreach ($form as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()] = $error->getMessage();
                }
            }
        }
        return $errors;
    }
}
