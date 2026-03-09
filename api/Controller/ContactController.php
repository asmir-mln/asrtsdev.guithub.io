<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
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

    public function __construct(
        NotificationService $notificationService,
        ReportService $reportService,
        LoggerInterface $logger
    ) {
        $this->notificationService = $notificationService;
        $this->reportService = $reportService;
        $this->logger = $logger;
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
