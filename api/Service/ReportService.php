<?php

namespace App\Service;

use App\Entity\Donation;
use App\Entity\Commande;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Service de génération de rapports complets
 * Crée des récapitulatifs détaillés pour donations, contacts, partenariats
 */
class ReportService
{
    private $mailer;
    private $em;
    private $logger;
    private $contactEmail;
    private $partnershipEmail;
    private $fromEmail;

    public function __construct(
        MailerInterface $mailer,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->logger = $logger;
        
        $this->fromEmail = $_ENV['MAILER_FROM'] ?? 'noreply@asartsdev.com';
        $this->contactEmail = $_ENV['CONTACT_EMAIL'] ?? $_ENV['ADMIN_EMAIL'] ?? 'noreply@asartsdev.com';
        $this->partnershipEmail = $_ENV['PARTNERSHIP_EMAIL'] ?? $_ENV['ADMIN_EMAIL'] ?? 'noreply@asartsdev.com';
    }

    /**
     * 📊 Générer rapport donation complète
     */
    public function generateDonationReport(Donation $donation): string
    {
        $commande = $donation->getCommande();
        $montant = $donation->getMontant();
        $nomDonateur = $donation->getNomDonateur() ?? 'Donateurs Anonymes';
        $email = $donation->getEmailDonateur() ?? 'anonyme@';
        $code = $donation->getCodeSuivi();

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Georgia', serif; background: #f5f5f5; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 50px; border-left: 5px solid #00f3ff; }
        .header { border-bottom: 3px solid #00f3ff; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #00f3ff; font-size: 2em; margin: 0; }
        .header .subtitle { color: #0099ff; font-style: italic; }
        .section { margin: 30px 0; padding: 20px; background: #f9f9f9; border-left: 4px solid #00d4ff; }
        .section h3 { color: #0099ff; margin-top: 0; }
        .table-data { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table-data td { padding: 10px; border-bottom: 1px solid #ddd; }
        .table-data .label { font-weight: bold; width: 30%; color: #00f3ff; }
        .impact { background: linear-gradient(135deg, rgba(0,243,255,0.1), rgba(0,153,255,0.1)); padding: 20px; border-left: 4px solid #00f3ff; }
        .impact h4 { color: #00f3ff; margin-top: 0; }
        .footer { text-align: center; padding-top: 20px; border-top: 1px solid #ddd; margin-top: 40px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 RAPPORT DONATION</h1>
            <p class="subtitle">AsArt'sDev - Soutien à l'innovation créative</p>
        </div>

        <div class="section">
            <h3>👤 Informations du Donateur</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Nom:</td>
                    <td>{$nomDonateur}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{$email}</td>
                </tr>
                <tr>
                    <td class="label">Téléphone:</td>
                    <td>{$donation->getTelephoneDonateur() ?? 'Non fourni'}</td>
                </tr>
                <tr>
                    <td class="label">Adresse:</td>
                    <td>{$donation->getAdresseDonateur() ?? 'Anonyme'}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>💰 Détails de la Donation</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Montant:</td>
                    <td><strong>{$montant}€</strong></td>
                </tr>
                <tr>
                    <td class="label">Devise:</td>
                    <td>{$donation->getDevise()}</td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td>{$donation->getDateDonation()->format('d/m/Y H:i:s')}</td>
                </tr>
                <tr>
                    <td class="label">Numero Commande:</td>
                    <td>#{$commande->getNumeroCommande()}</td>
                </tr>
                <tr>
                    <td class="label">Code Suivi:</td>
                    <td><code>{$code}</code></td>
                </tr>
                <tr>
                    <td class="label">Statut:</td>
                    <td>{$donation->getStatutSuivi()}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>🎁 Contrepartie</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Eligible:</td>
                    <td>{$donation->isEligibleCadeau() ? '✅ OUI' : '❌ NON'}</td>
                </tr>
                <tr>
                    <td class="label">Type:</td>
                    <td>{$donation->getStatutCadeauLisible()}</td>
                </tr>
                <tr>
                    <td class="label">Envoyé:</td>
                    <td>{$donation->getCadeauEnvoye() ? $donation->getCadeauEnvoye()->format('d/m/Y') : 'En attente'}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3>🔍 Traçabilité</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Adresse IP:</td>
                    <td><code>{$donation->getIpAdresse()}</code></td>
                </tr>
                <tr>
                    <td class="label">User Agent:</td>
                    <td style="font-size: 0.9em; color: #666;">{$donation->getUserAgent()}</td>
                </tr>
            </table>
        </div>

        <div class="impact">
            <h4>💡 Impact de cette donation</h4>
            <ul style="margin: 10px 0;">
                <li>Soutien direct aux projets AsArt'sDev</li>
                <li>Contribution à l'accessibilité et l'inclusion technologique</li>
                <li>Financement de l'innovation créative</li>
                <li>Reconnaissance en tant que partenaire de la vision inclusive</li>
            </ul>
        </div>

        <div class="footer">
            <p>Rapport généré automatiquement - AsArt'sDev</p>
            <p>{date('d/m/Y H:i:s')}</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * 📧 Envoyer rapport donation
     */
    public function sendDonationReport(Donation $donation): bool
    {
        try {
            $html = $this->generateDonationReport($donation);

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($this->contactEmail)
                ->subject('📊 Rapport Donation: ' . $donation->getMontant() . '€ - ' . $donation->getCodeSuivi())
                ->html($html);

            $this->mailer->send($email);

            $this->logger->info('Rapport donation envoyé', [
                'donation_id' => $donation->getId(),
                'to' => $this->contactEmail
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur envoi rapport donation', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 📋 Rapport de message de contact/partenariat
     */
    public function generateContactReport(array $data): string
    {
        $type = $data['type'] ?? 'contact';
        $nom = $data['nom'] ?? 'Non fourni';
        $email = $data['email'] ?? 'Non fourni';
        $sujet = $data['sujet'] ?? 'Sans sujet';
        $message = $data['message'] ?? '';
        $organisationMecene = $data['organisation_mecenat'] ?? null;
        $typeMecenat = $data['type_mecenat'] ?? null;

        $icone = match($type) {
            'partenariat' => '🤝',
            'mecenat' => '💝',
            'proposition' => '💡',
            default => '💬'
        };

        $typeLabel = match($type) {
            'partenariat' => 'DEMANDE DE PARTENARIAT',
            'mecenat' => 'PROPOSITION DE MÉCÉNAT',
            'proposition' => 'PROPOSITION STRATÉGIQUE',
            default => 'MESSAGE DE CONTACT'
        };

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Georgia', serif; background: #f5f5f5; color: #333; line-height: 1.7; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 50px; border-left: 5px solid #00f3ff; }
        .header { border-bottom: 3px solid #00f3ff; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #00f3ff; font-size: 2.2em; margin: 0; }
        .badge { display: inline-block; background: #00f3ff; color: #000; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 0.85em; }
        .section { margin: 30px 0; padding: 20px; background: #f9f9f9; border-left: 4px solid #00d4ff; }
        .section h3 { color: #0099ff; margin-top: 0; }
        .table-data { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table-data td { padding: 12px; border-bottom: 1px solid #ddd; }
        .table-data .label { font-weight: bold; width: 30%; color: #00f3ff; }
        .message-box { background: #fffacd; padding: 20px; border-left: 4px solid #ffc107; margin: 20px 0; white-space: pre-wrap; font-family: Arial; }
        .mecenat-section { background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,152,0,0.1)); padding: 20px; border-left: 4px solid #ff9800; }
        .footer { text-align: center; padding-top: 20px; border-top: 1px solid #ddd; margin-top: 40px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$icone} {$typeLabel}</h1>
            <span class="badge">{$type}</span>
        </div>

        <div class="section">
            <h3>👤 Informations du Contact</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Nom:</td>
                    <td>{$nom}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td>{$email}</td>
                </tr>
                <tr>
                    <td class="label">Sujet:</td>
                    <td><strong>{$sujet}</strong></td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td>{date('d/m/Y H:i:s')}</td>
                </tr>
            </table>
        </div>

        {$this->renderMecenatSection($organisationMecene, $typeMecenat)}

        <div class="section">
            <h3>📝 Message</h3>
            <div class="message-box">{htmlspecialchars($message)}</div>
        </div>

        <div class="footer">
            <p><strong>⚠️ ACTION REQUISE:</strong> Répondre à {$email}</p>
            <p>Rapport généré - AsArt'sDev | {date('d/m/Y H:i:s')}</p>
        </div>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * 💝 Section mécénat si applicable
     */
    private function renderMecenatSection(?string $organisation, ?string $type): string
    {
        if (!$organisation || !$type) {
            return '';
        }

        return <<<HTML
        <div class="mecenat-section">
            <h3>💝 Proposition de Mécénat</h3>
            <table class="table-data">
                <tr>
                    <td class="label">Organisation:</td>
                    <td><strong>{$organisation}</strong></td>
                </tr>
                <tr>
                    <td class="label">Type de soutien:</td>
                    <td>{$type}</td>
                </tr>
            </table>
        </div>
HTML;
    }

    /**
     * 📧 Envoyer rapport contact/partenariat
     */
    public function sendContactReport(array $data): bool
    {
        try {
            $html = $this->generateContactReport($data);
            
            $type = $data['type'] ?? 'contact';
            $sujet = $data['sujet'] ?? 'Sans sujet';

            $email = (new Email())
                ->from($this->fromEmail)
                ->to($this->contactEmail)
                ->subject('📋 Rapport ' . ucfirst($type) . ': ' . $sujet)
                ->html($html);

            $this->mailer->send($email);

            $this->logger->info('Rapport contact envoyé', [
                'type' => $type,
                'to' => $this->contactEmail
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Erreur envoi rapport contact', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
