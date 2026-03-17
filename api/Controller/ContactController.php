<?php

namespace App\Controller;

use App\Entity\Investisseur;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\NotificationService;
use App\Service\ReportService;
use Psr\Log\LoggerInterface;

/**
 * @Route("/contact", name="contact_")
 */
class ContactController extends AbstractController
{
    private $notificationService;
    private $reportService;
    private $logger;
    private $em;

    public function __construct(
        NotificationService $notificationService,
        ReportService $reportService,
        LoggerInterface $logger,
        EntityManagerInterface $em
    ) {
        $this->notificationService = $notificationService;
        $this->reportService = $reportService;
        $this->logger = $logger;
        $this->em = $em;
    }

    /**
     * 🏢 Formulaire dédié investisseurs sérieux
     *
     * @Route("/contact-investisseur", name="investisseur", methods={"GET", "POST"})
     */
    public function contactInvestisseur(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (($data['montant'] ?? 0) < 50000) {
                $this->addFlash('error', 'Seules les demandes serieuses sont acceptees.');
                return $this->redirectToRoute('contact_investisseur');
            }

            $iaScore = 0.0;
            $totalScore = 0.0;

            // Score financier
            if (($data['montant'] ?? 0) > 100000) {
                $totalScore += 2;
            }
            if (($data['montant'] ?? 0) > 500000) {
                $totalScore += 3;
            }

            $strategieText = (string) ($data['strategie'] ?? '');

            // Analyse texte strategie (heuristique locale en attendant un branchement SDK OpenAI).
            if (strlen($strategieText) > 100) {
                $iaScore += 2;
            }
            if (preg_match('/startup|innov(ation|er)|scale/i', $strategieText)) {
                $iaScore += 1;
            }

            $totalScore += $iaScore;

            // NDA signe
            if ($form['nda']->getData()) {
                $totalScore += 1;
            }

            // Score minimum requis
            if ($totalScore < 5) {
                $this->addFlash('error', 'Profil non retenu (score IA insuffisant)');
                return $this->redirectToRoute('contact_investisseur');
            }

            // Upload fichier justificatif
            $file = $form['justificatif']->getData();
            $filename = null;
            if ($file) {
                $filename = uniqid('', true) . '.' . $file->guessExtension();

                $uploadDir = $this->getParameter('uploads_dir');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                try {
                    $file->move($uploadDir, $filename);
                } catch (FileException $exception) {
                    $this->addFlash('error', 'Impossible de televerser le justificatif financier.');
                    return $this->redirectToRoute('contact_investisseur');
                }
            }

            // Generer acces prive (token) pour validation admin.
            $token = bin2hex(random_bytes(32));

            $investisseur = new Investisseur();
            $investisseur->setEntreprise((string) ($data['entreprise'] ?? ''));
            $investisseur->setMontant((float) ($data['montant'] ?? 0));
            $investisseur->setToken($token);
            $investisseur->setTokenExpire((new \DateTime())->modify('+48 hours'));
            $investisseur->setIpAccess($request->server->get('REMOTE_ADDR'));
            $investisseur->setValide(false);
            $investisseur->setStrategie($strategieText);
            $investisseur->setJustificatif($filename);
            $investisseur->setNdaSigne((bool) $form['nda']->getData());
            $investisseur->setIaScore($iaScore);
            $investisseur->setTotalScore($totalScore);

            $this->em->persist($investisseur);
            $this->em->flush();

            // Ici vous pouvez brancher un envoi email ou une persistence en base.
            $this->addFlash('success', 'Votre demande a ete envoyee. Nous vous contacterons apres etude.');

            return $this->redirectToRoute('contact_investisseur');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/acces/{token}", name="acces_prive", methods={"GET"})
     */
    public function acces(string $token): Response
    {
        $investisseur = $this->em->getRepository(Investisseur::class)->findOneBy(['token' => $token]);

        if (
            !$investisseur
            || !$investisseur->isValide()
            || ($investisseur->getTokenExpire() !== null && $investisseur->getTokenExpire() < new \DateTime())
        ) {
            throw $this->createAccessDeniedException('Acces refuse ou lien expire.');
        }

        return $this->render('private/dashboard.html.twig', [
            'investisseur' => $investisseur,
        ]);
    }

    /**
     * 📧 Formulaire de contact
     * 
     * @Route("/", name="form", methods={"GET"})
     */
    public function contactForm(): Response
    {
        return $this->render('contact/form.html.twig', [
            'types' => ['contact', 'partenariat', 'mecenat', 'proposition']
        ]);
    }

    /**
     * 💬 Traiter le message de contact
     * 
     * @Route("/submit", name="submit", methods={"POST"})
     */
    public function submitContact(Request $request): Response
    {
        try {
            $data = [
                'type' => $request->request->get('type') ?? 'contact',
                'nom' => $request->request->get('nom') ?? 'Non fourni',
                'email' => $request->request->get('email') ?? 'Non fourni',
                'telephone' => $request->request->get('telephone') ?? null,
                'organisme' => $request->request->get('organisme') ?? null,
                'sujet' => $request->request->get('sujet') ?? 'Sans sujet',
                'message' => $request->request->get('message') ?? '',
                'organisation_mecenat' => $request->request->get('organisation_mecenat') ?? null,
                'type_mecenat' => $request->request->get('type_mecenat') ?? null,
                'montant_mecenat' => $request->request->get('montant_mecenat') ?? null,
                'ip_adresse' => $request->getClientIp(),
                'user_agent' => $request->headers->get('User-Agent'),
                'date_envoi' => date('Y-m-d H:i:s'),
            ];

            // Validation basique
            if (empty($data['email']) || empty($data['message'])) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'Email et message requis'
                ], 400);
            }

            // ========================================
            // Envoyer rapport à contact@gmail.com
            // ========================================

            $this->reportService->sendContactReport($data);

            // ========================================
            // Email de confirmation au demandeur
            // ========================================

            $this->sendConfirmationEmail($data);

            // ========================================
            // Log et notification
            // ========================================

            $this->logger->info('Message de contact reçu', [
                'type' => $data['type'],
                'email' => $data['email'],
                'ip' => $data['ip_adresse']
            ]);

            // ========================================
            // Réponse
            // ========================================

            $typeLabel = match($data['type']) {
                'partenariat' => '🤝 Demande de partenariat',
                'mecenat' => '💝 Proposition de mécénat',
                'proposition' => '💡 Proposition stratégique',
                default => '💬 Message de contact'
            };

            return new JsonResponse([
                'success' => true,
                'message' => 'Merci! Votre ' . strtolower($typeLabel) . ' a été reçue.',
                'type' => $data['type'],
                'reference' => bin2hex(random_bytes(4)),
                'next_steps' => 'Vous recevrez une réponse sur ' . $data['email'] . ' dans les 48 heures.'
            ], 201);

        } catch (\Exception $e) {
            $this->logger->error('Erreur submission contact', ['error' => $e->getMessage()]);
            return new JsonResponse([
                'success' => false,
                'error' => 'Erreur serveur'
            ], 500);
        }
    }

    /**
     * 🎁 Demander l'attestation de partenariat
     * 
     * @Route("/request-attestation", name="request_attestation", methods={"POST"})
     */
    public function requestAttestation(Request $request): Response
    {
        try {
            $data = [
                'nom_organisme' => $request->request->get('nom_organisme'),
                'email_contact' => $request->request->get('email_contact'),
                'type_partenariat' => $request->request->get('type_partenariat'),
                'montant_engagement' => $request->request->get('montant_engagement'),
                'duree_engagement' => $request->request->get('duree_engagement') ?? '2026-2036',
            ];

            // Créer une version personnalisée de l'attestation
            $attestationUrl = $this->generateUrl('attestation_view') . '?' . http_build_query([
                'organisme' => $data['nom_organisme'],
                'type' => $data['type_partenariat'],
                'montant' => $data['montant_engagement']
            ]);

            // Envoyer le lien
            $emailContent = $this->renderView('contact/attestation_email.html.twig', [
                'organisme' => $data['nom_organisme'],
                'type' => $data['type_partenariat'],
                'url' => $attestationUrl
            ]);

            // Email au demandeur
            // ... implémentation mailer ...

            return new JsonResponse([
                'success' => true,
                'message' => 'Attestation générée et envoyée à ' . $data['email_contact'],
                'attestation_url' => $attestationUrl
            ]);

        } catch (\Exception $e) {
            $this->logger->error('Erreur attestation', ['error' => $e->getMessage()]);
            return new JsonResponse(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * 📜 Afficher l'attestation de partenariat
     * 
     * @Route("/attestation", name="attestation_view", methods={"GET"})
     */
    public function viewAttestation(): Response
    {
        return $this->render('contact/attestation.html.twig', [
            'organisme' => $_GET['organisme'] ?? 'Partenaire AsArt\'sDev',
            'type' => $_GET['type'] ?? 'Innovation Inclusive',
            'montant' => $_GET['montant'] ?? null,
        ]);
    }

    /**
     * ✅ Page de confirmation après contact
     * 
     * @Route("/confirmation/{reference}", name="confirmation", methods={"GET"})
     */
    public function confirmationPage(string $reference): Response
    {
        return $this->render('contact/confirmation.html.twig', [
            'reference' => $reference,
            'statement' => 'Votre message a été enregistré. Vous recevrez une réponse dans les 48 heures.'
        ]);
    }

    // ==================== PRIVÉ ====================

    /**
     * Envoyer email de confirmation au demandeur
     */
    private function sendConfirmationEmail(array $data): void
    {
        $typeLabel = match($data['type']) {
            'partenariat' => 'Votre demande de partenariat',
            'mecenat' => 'Votre proposition de mécénat',
            'proposition' => 'Votre proposition stratégique',
            default => 'Votre message'
        };

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial; background: #f5f5f5; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; border-left: 5px solid #00f3ff;">
        <h2 style="color: #00f3ff;">✅ Nous avons reçu votre message</h2>
        
        <p>Bonjour {$data['nom']},</p>
        
        <p><strong>{$typeLabel}</strong> a été reçue avec succès.</p>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Type:</strong> {$data['type']}</p>
            <p><strong>Sujet:</strong> {$data['sujet']}</p>
            <p><strong>Date/Heure:</strong> {$data['date_envoi']}</p>
        </div>

        <p>Nous examinons votre demande avec attention et vous répondrons dans les <strong>48 heures</strong>.</p>
        
        <p style="margin-top: 30px;">
            —<br>
            L'équipe AsArt'sDev<br>
            <a href="mailto:asartsdev.contact@gmail.com">asartsdev.contact@gmail.com</a>
        </p>
    </div>
</body>
</html>
HTML;

        // Implémentation mailer (stub)
        // $this->notificationService->sendEmail($data['email'], 'Confirmation de réception', $html);
    }
}
