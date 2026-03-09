<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Psr\Log\LoggerInterface;

/**
 * Service d'envoi de notifications par email
 * Envoie les notifications de donations, reminders, et alertes admin
 */
class NotificationService
{
    private $mailer;
    private $params;
    private $logger;
    private $adminEmail;
    private $fromEmail;

    public function __construct(
        MailerInterface $mailer,
        ParameterBagInterface $params,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->params = $params;
        $this->logger = $logger;
        
        // Récupérer les emails depuis .env
        $this->adminEmail = $_ENV['ADMIN_EMAIL'] ?? $_ENV['MAILER_FROM'] ?? 'noreply@asartsdev.com';
        $this->fromEmail = $_ENV['MAILER_FROM'] ?? 'noreply@asartsdev.com';
    }

    /**
     * 📧 Envoyer email de confirmation de donation
     */
    public function senDonationConfirmation(
        \App\Entity\Donation $donation,
        string $recipientEmail
    ): bool {
        try {
            $commande = $donation->getCommande();
            $montant = $donation->getMontant();

            $html = $this->renderDonationConfirmationHTML($donation);

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($recipientEmail)
                ->subject('✅ Votre donation AsArt\'sDev a été confirmée')
                ->html($html);

            $this->mailer->send($email);

            $this->logger->info('Email confirmation donation envoyé', [
                'donation_id' => $donation->getId(),
                'to' => $recipientEmail,
                'montant' => $montant
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur envoi email confirmation', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * 🔔 Notifier l'admin d'une nouvelle donation
     */
    public function notifyAdminNewDonation(\App\Entity\Donation $donation): bool
    {
        try {
            $commande = $donation->getCommande();
            $montant = $donation->getMontant();
            $donateur = $donation->getIdentificationDonateur();

            $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial; background: #f5f5f5;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px;">
        <h2 style="color: #00f3ff;">🎁 Nouvelle Donation!</h2>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background: #f0f0f0;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Donateur</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$donateur}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Montant</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$montant}€</td>
            </tr>
            <tr style="background: #f0f0f0;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Date</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$donation->getDateDonation()->format('d/m/Y H:i')}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>IP</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><code>{$donation->getIpAdresse()}</code></td>
            </tr>
            <tr style="background: #f0f0f0;">
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Commande</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">#{$commande->getNumeroCommande()}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Cadeau</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$donation->getStatutCadeauLisible()}</td>
            </tr>
        </table>

        <p style="margin: 20px 0;">
            <a href="https://asartsdev.com/admin/donation/{$donation->getId()}" 
               style="background: #00f3ff; color: #000; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                → Voir détails dans admin
            </a>
        </p>

        <hr style="border: none; border-top: 1px solid #ddd;">
        <p style="font-size: 0.85em; color: #666;">
            Notification automatique AsArt'sDev | {$donation->getDateDonation()->format('d/m/Y H:i')}
        </p>
    </div>
</body>
</html>
HTML;

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($this->adminEmail)
                ->subject('🎁 Nouvelle donation: ' . $montant . '€')
                ->html($html);

            $this->mailer->send($email);

            $this->logger->info('Notification admin donation envoyée', [
                'donation_id' => $donation->getId(),
                'montant' => $montant
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur notification admin', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * ⚠️ Alerte admin - Donation suspecte
     */
    public function alertAdminSuspiciousDonation(
        \App\Entity\Donation $donation,
        string $raison
    ): bool {
        try {
            $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family: Arial; background: #fff3cd;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; border-left: 5px solid #ff9933;">
        <h2 style="color: #ff6600;">⚠️ Alerte: Donation Suspecte</h2>
        
        <p style="background: #fffacd; padding: 15px; border-radius: 5px; border-left: 4px solid #ff9933;">
            <strong>Raison:</strong> {$raison}
        </p>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>IP</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><code>{$donation->getIpAdresse()}</code></td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Email</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$donation->getEmailDonateur() ?? 'ANONYME'}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><strong>Montant</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd;">{$donation->getMontant()}€</td>
            </tr>
        </table>

        <p style="margin: 20px 0;">
            <a href="https://asartsdev.com/admin/security/ips" 
               style="background: #ff6600; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">
                → Voir tous les signalements
            </a>
        </p>
    </div>
</body>
</html>
HTML;

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($this->adminEmail)
                ->subject('⚠️ Alerte: Donation suspecte')
                ->html($html);

            $this->mailer->send($email);

            $this->logger->warning('Alerte donation suspecte envoyée', [
                'donation_id' => $donation->getId(),
                'raison' => $raison
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur alerte donation', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 📧 HTML pour email de confirmation donation
     */
    private function renderDonationConfirmationHTML(\App\Entity\Donation $donation): string
    {
        $commande = $donation->getCommande();
        $montant = $donation->getMontant();
        $codeSuivi = $donation->getCodeSuivi();
        $nomDonateur = $donation->getNomDonateur() ?? 'Donateur anonyme';

        $cadeau = $donation->isEligibleCadeau() 
            ? '<p style="color: #4caf50; font-weight: bold;">✅ Vous êtes éligible à une contrepartie!</p>'
            : '<p style="color: #ff6600;">ℹ️ Donation anonyme - Pas de contrepartie</p>';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; border-top: 5px solid #00f3ff; }
        .header { border-bottom: 2px solid #00f3ff; padding-bottom: 15px; }
        .header h1 { color: #00f3ff; margin: 0; font-size: 1.8em; }
        .content { margin: 20px 0; line-height: 1.6; }
        .info-box { background: #f0f8ff; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #00f3ff; }
        .tracking { background: #fffacd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #ffc107; }
        .tracking code { background: #f5f5f5; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
        .footer { border-top: 1px solid #ddd; padding-top: 15px; font-size: 0.85em; color: #666; margin-top: 20px; }
        .button { display: inline-block; background: #00f3ff; color: #000; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Merci pour votre donation!</h1>
            <p>Votre soutien à AsArt'sDev est confirmé</p>
        </div>

        <div class="content">
            <p>Bonjour {$nomDonateur},</p>

            <p>Nous avons bien reçu votre donation de <strong>{$montant}€</strong> le <strong>{$commande->getDateCreation()->format('d/m/Y à H:i')}</strong>.</p>

            <div class="info-box">
                <strong>📊 Récapitulatif de votre donation:</strong><br>
                <strong>Commande:</strong> #{$commande->getNumeroCommande()}<br>
                <strong>Montant:</strong> {$montant}€<br>
                <strong>Statut:</strong> Confirmée ✅<br>
                <strong>Type:</strong> {$commande->getTypeProjet()}
            </div>

            {$cadeau}

            <div class="tracking">
                <strong>🔍 Suivi de votre donation:</strong><br>
                <code>{$codeSuivi}</code><br>
                <p style="margin-top: 10px; font-size: 0.9em;">Utilisez ce code pour suivre votre donation sur le site</p>
            </div>

            <p style="margin-top: 20px;">Vous recevrez des mises à jour régulières sur l'avancement de votre cadeau/contrepartie jusqu'à sa livraison.</p>

            <p style="margin-top: 20px;">
                <a href="https://asartsdev.com/admin/track/{$codeSuivi}" class="button">
                    → Suivre ma donation
                </a>
            </p>
        </div>

        <div class="footer">
            <p><strong>AsArt'sDev</strong> - Innovation & Créativité</p>
            <p>Pour toute question: <a href="mailto:sam.mln51@icloud.com">sam.mln51@icloud.com</a></p>
            <p style="margin-top: 10px; opacity: 0.7;">Ce message a été envoyé automatiquement. Merci de ne pas y répondre directement.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Test d'envoi d'email
     */
    public function testEmail(string $testEmail): bool
    {
        try {
            $html = <<<HTML
<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head><body>
<div style="font-family: Arial; padding: 20px; background: #f5f5f5;">
    <h2>✅ Email de test reçu!</h2>
    <p>Ce test confirme que le système d'email est opérationnel.</p>
    <p><strong>Date:</strong> {$this->getNow()}</p>
</div>
</body></html>
HTML;

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($testEmail)
                ->subject('✅ Test email AsArt\'sDev')
                ->html($html);

            $this->mailer->send($email);

            $this->logger->info('Email de test envoyé', ['to' => $testEmail]);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur test email', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Getter admin email
     */
    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }

    /**
     * Setter admin email
     */
    public function setAdminEmail(string $email): self
    {
        $this->adminEmail = $email;
        return $this;
    }

    /**
     * Helper date actuelle
     */
    private function getNow(): string
    {
        return (new \DateTime())->format('d/m/Y H:i:s');
    }
}
