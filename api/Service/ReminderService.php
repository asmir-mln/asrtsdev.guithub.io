<?php

namespace App\Service;

use App\Entity\ReminderCommande;
use App\Entity\Donation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Service de gestion des reminders et notifications
 */
class ReminderService
{
    private $em;
    private $mailer;
    private $fromEmail;

    public function __construct(
        EntityManagerInterface $em,
        MailerInterface $mailer,
        string $fromEmail = 'noreply@asartsdev.com'
    ) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->fromEmail = $fromEmail;
    }

    /**
     * Envoyer un reminder par email
     */
    public function sendReminder(ReminderCommande $reminder, string $emailDestinaire): bool
    {
        try {
            $commande = $reminder->getCommande();
            $message = $reminder->getMessage();

            // Construire l'email
            $email = (new Email())
                ->from($this->fromEmail)
                ->to($emailDestinaire)
                ->subject($reminder->getTypeRemainderLisible())
                ->html($this->renderReminderHTML($reminder));

            // Envoyer
            $this->mailer->send($email);

            return true;
        } catch (\Exception $e) {
            // Log erreur...
            return false;
        }
    }

    /**
     * Créer automatiquement un reminder lors du changement de statut commande
     */
    public function createReminderForCommande(
        \App\Entity\Commande $commande,
        string $typeReminder,
        string $message = null
    ): ReminderCommande {
        $reminder = new ReminderCommande();
        $reminder->setCommande($commande);
        $reminder->setTypeReminder($typeReminder);
        $reminder->setMessage($message ?? $this->generateDefaultMessage($typeReminder, $commande));
        $reminder->setStatut('brouillon');

        $this->em->persist($reminder);
        $this->em->flush();

        return $reminder;
    }

    /**
     * Générer le message HTML du reminder
     */
    private function renderReminderHTML(ReminderCommande $reminder): string
    {
        $commande = $reminder->getCommande();
        $donations = $commande->getDonations();
        
        $donationInfo = '';
        if (!$donations->isEmpty()) {
            $donation = $donations->first();
            $donationInfo = sprintf(
                '<p><strong>Montant:</strong> %.2f€</p>
                 <p><strong>Code suivi:</strong> %s</p>',
                $donation->getMontant(),
                $donation->getCodeSuivi()
            );
        }

        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { border-bottom: 3px solid #00f3ff; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #00f3ff; margin: 0; }
        .content { line-height: 1.6; color: #333; }
        .footer { border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; font-size: 12px; color: #666; }
        .badge { display: inline-block; padding: 5px 10px; background: #00f3ff; color: #000; border-radius: 4px; font-weight: bold; }
        .alert { background: #fffacd; padding: 10px; border-left: 4px solid #ff9933; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎁 Rappel AsArt'sDev</h1>
            <p>{$reminder->getTypeRemainderLisible()}</p>
        </div>

        <div class="content">
            <p>Bonjour,</p>
            
            <p><strong>Commande:</strong> {$commande->getNumeroCommande()}</p>
            <p><strong>Statut:</strong> <span class="badge">{$commande->getStatut()}</span></p>
            
            {$donationInfo}
            
            <div class="alert">
                <p><strong>{$reminder->getMessage()}</strong></p>
            </div>

            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <p>Merci pour votre soutien! 🙏</p>
        </div>

        <div class="footer">
            <p>AsArt'sDev | Innovation & Créativité</p>
            <p>Ce message a été envoyé automatiquement, merci de ne pas y répondre</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Générer un message par défaut selon le type de reminder
     */
    private function generateDefaultMessage(string $type, \App\Entity\Commande $commande): string
    {
        return match($type) {
            'creation' => '📝 Votre donation a été reçue et enregistrée avec succès.',
            'paiement_recu' => '✅ Votre paiement a été confirme. Votre contrepartie est en préparation.',
            'preparation' => '🎁 Votre cadeau/contrepartie est actuellement en préparation.',
            'pret_expedition' => '📦 Votre colis est prêt et sera expédié demain.',
            'envoye' => '🚚 Votre colis a été expédié. Vous recevrez le numéro de suivi par email.',
            'recu' => '📬 Avez-vous reçu votre contrepartie? Nous aimerions connaitre votre avis.',
            'relance' => '⏰ Relance concernant votre donation #' . $commande->getNumeroCommande(),
            default => 'Notification concernant votre commande #' . $commande->getNumeroCommande()
        };
    }

    /**
     * Envoyer tous les reminders en attente
     */
    public function sendAllPendingReminders(): array
    {
        $remindersRepo = $this->em->getRepository(ReminderCommande::class);
        $pendingReminders = $remindersRepo->findBy(['statut' => 'brouillon']);

        $results = [
            'sent' => 0,
            'failed' => 0,
            'non_applicable' => 0,
        ];

        foreach ($pendingReminders as $reminder) {
            $commande = $reminder->getCommande();
            $donations = $commande->getDonations();

            if ($donations->isEmpty()) {
                $reminder->setStatut('non_applicable');
                $results['non_applicable']++;
                continue;
            }

            $donation = $donations->first();

            // Si donation anonyme, ne pas envoyer
            if ($donation->isAnonyme()) {
                $reminder->setStatut('non_applicable');
                $results['non_applicable']++;
                continue;
            }

            // Envoyer le reminder
            if ($this->sendReminder($reminder, $donation->getEmailDonateur())) {
                $reminder->marquerEnvoye($donation->getEmailDonateur());
                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        $this->em->flush();

        return $results;
    }

    /**
     * Générer un rapport de suivi pour un donateur
     */
    public function generateTrackingReport(Donation $donation): array
    {
        $commande = $donation->getCommande();
        $reminders = $commande->getReminders();

        $timeline = [];
        foreach ($reminders as $reminder) {
            $timeline[] = [
                'date' => $reminder->getDateCreation(),
                'type' => $reminder->getTypeRemainderLisible(),
                'message' => $reminder->getMessage(),
                'envoye' => $reminder->estEnvoye(),
            ];
        }

        // Trier par date
        usort($timeline, function($a, $b) {
            return $a['date'] <=> $b['date'];
        });

        return [
            'donation' => [
                'montant' => $donation->getMontant(),
                'date' => $donation->getDateDonation(),
                'estatut' => $donation->getStatutSuivi(),
            ],
            'commande' => [
                'numero' => $commande->getNumeroCommande(),
                'statut' => $commande->getStatut(),
                'type' => $commande->getTypeProjet(),
            ],
            'cadeau' => [
                'eligible' => $donation->isEligibleCadeau(),
                'id' => $donation->getCadeauId(),
                'envoye' => $donation->getCadeauEnvoye(),
            ],
            'timeline' => $timeline,
        ];
    }
}
