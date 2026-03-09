<?php

namespace App\Service;

use App\Entity\Livre;
use App\Entity\Commande;
use App\Entity\Livraison;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class LivreService
{
    private $entityManager;
    private $logger;
    private $notificationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        NotificationService $notificationService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->notificationService = $notificationService;
    }

    /**
     * Estime le prix de livraison selon les options
     */
    public function estimerPrixLivraison(array $options): array
    {
        $livre = $options['livre'];
        $typeEnvoi = $options['type_envoi'] ?? 'standard';
        $dedicaceDemandee = $options['dedicace_demandee'] ?? false;
        $pays = $options['pays'] ?? 'france';

        // Prix de base
        $prixBase = 5.69; // Colissimo suivi
        $delaiLivraison = 5; // jours

        // Tarification selon le type d'envoi
        switch ($typeEnvoi) {
            case 'standard':
                $prixBase = 5.69;
                $delaiLivraison = 5;
                break;

            case 'express':
                $prixBase = 8.99;
                $delaiLivraison = 2;
                break;

            case 'express_retour':
                $prixBase = 15.00;
                $delaiLivraison = 1;
                break;

            case 'dedicace':
                // Envoi spécial avec dédicace personnalisée
                $prixBase = 30.00; // Inclut aller-retour + dédicace
                $delaiLivraison = 3;
                break;
        }

        // Supplément pour dédicace si ajoutée à un envoi normal
        $supplementDedicace = 0;
        if ($dedicaceDemandee && $typeEnvoi !== 'dedicace') {
            $supplementDedicace = 25.00;
            $delaiLivraison += 2; // Temps supplémentaire pour dédicace
        }

        // Calcul selon le poids du livre
        $poidsKg = $livre->getPoidsKg();
        $supplementPoids = 0;

        // Si le livre dépasse 1kg, supplément de 2€/kg
        if ($poidsKg > 1.0) {
            $supplementPoids = ceil($poidsKg - 1.0) * 2.00;
        }

        // Assurance pour livres de plus de 100€
        $prixAssurance = 0;
        if ($livre->getPrix() > 100) {
            $prixAssurance = 5.00;
        }

        // Total
        $prixTotal = $prixBase + $supplementDedicace + $supplementPoids + $prixAssurance;

        // Ajustement selon le pays
        switch (strtolower($pays)) {
            case 'europe':
                $prixTotal += 6.80;
                $delaiLivraison += 5;
                break;

            case 'monde':
                $prixTotal += 19.30;
                $delaiLivraison += 15;
                break;
        }

        return [
            'prix_base' => $prixBase,
            'supplement_dedicace' => $supplementDedicace,
            'supplement_poids' => $supplementPoids,
            'prix_assurance' => $prixAssurance,
            'prix_total' => $prixTotal,
            'delai_livraison' => $delaiLivraison,
            'type_envoi' => $typeEnvoi,
            'transporteur' => $this->getTransporteur($typeEnvoi),
            'details' => $this->getDetailsLivraison($typeEnvoi, $dedicaceDemandee)
        ];
    }

    /**
     * Crée une commande pour un livre
     */
    public function creerCommandeLivre(array $options): Commande
    {
        $livre = $options['livre'];
        $estimation = $this->estimerPrixLivraison($options);

        // Créer la commande
        $commande = new Commande();
        $commande->setNumeroCommande('LIV-' . strtoupper(uniqid()));
        $commande->setMontant($livre->getPrix());
        $commande->setStatut('en_attente');
        $commande->setDateCreation(new \DateTime());

        // Informations client
        if (isset($options['client_nom'])) {
            $commande->setNomClient($options['client_nom']);
        }
        if (isset($options['client_email'])) {
            $commande->setEmailClient($options['client_email']);
        }

        $this->entityManager->persist($commande);

        // Créer la livraison associée
        $livraison = new Livraison();
        $livraison->setCommande($commande);
        $livraison->setStatut('en_attente');
        $livraison->setTypeEnvoi($options['type_envoi']);
        $livraison->setTransporteur($estimation['transporteur']);
        $livraison->setPrixBase($estimation['prix_base']);
        $livraison->setPrixTotal($estimation['prix_total']);
        $livraison->setDedicaceDemandee($options['dedicace_demandee'] ?? false);

        if ($options['dedicace_demandee'] && isset($options['dedicace_texte'])) {
            $livraison->setDedicaceTexte($options['dedicace_texte']);
            $livraison->setDedicaceMontantSupplementaire($estimation['supplement_dedicace']);
        }

        // Adresse de livraison
        if (isset($options['adresse_livraison'])) {
            $adresse = $options['adresse_livraison'];
            $livraison->setAdresseLivraison($adresse['rue'] ?? '');
            $livraison->setVilleLivraison($adresse['ville'] ?? '');
            $livraison->setCodePostal($adresse['code_postal'] ?? '');
            $livraison->setPaysLivraison($adresse['pays'] ?? 'France');
        }

        // Date de livraison estimée
        $dateEstimee = new \DateTime();
        $dateEstimee->modify('+' . $estimation['delai_livraison'] . ' days');
        $livraison->setDateLivraisonEstimee($dateEstimee);

        $this->entityManager->persist($livraison);

        // Diminuer le stock
        $livre->setStockDisponible($livre->getStockDisponible() - 1);

        $this->entityManager->flush();

        // Log
        $this->logger->info('Commande livre créée', [
            'commande_id' => $commande->getId(),
            'livre_id' => $livre->getId(),
            'type_envoi' => $options['type_envoi'],
            'dedicace' => $options['dedicace_demandee']
        ]);

        return $commande;
    }

    /**
     * Met à jour le statut de livraison et envoie des notifications
     */
    public function mettreAJourStatutLivraison(Livraison $livraison, string $nouveauStatut, array $details = []): void
    {
        $ancienStatut = $livraison->getStatut();
        $livraison->setStatut($nouveauStatut);
        $livraison->setDerniereMiseAJour(new \DateTime());

        // Mise à jour selon le statut
        switch ($nouveauStatut) {
            case 'preparation':
                $livraison->setDatePreparation(new \DateTime());
                break;

            case 'envoye':
                $livraison->setDateEnvoi(new \DateTime());
                if (isset($details['numero_suivi'])) {
                    $livraison->setNumeroSuivi($details['numero_suivi']);
                }
                break;

            case 'livre':
                $livraison->setDateLivraisonReelle(new \DateTime());
                break;
        }

        if (isset($details['localisation'])) {
            $livraison->setDerniereLocalisation($details['localisation']);
        }

        $this->entityManager->flush();

        // Créer une entrée dans l'historique de suivi
        $this->creerHistoriqueSuivi($livraison, $nouveauStatut, $details);

        // Envoyer notification automatique au client
        $this->notificationService->envoyerNotificationSuiviLivraison(
            $livraison,
            $ancienStatut,
            $nouveauStatut,
            $details
        );

        // Envoyer newsletter de suivi
        $this->envoyerNewsletterSuivi($livraison, $nouveauStatut);

        $this->logger->info('Statut livraison mis à jour', [
            'livraison_id' => $livraison->getId(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut
        ]);
    }

    /**
     * Crée une entrée dans l'historique de suivi
     */
    private function creerHistoriqueSuivi(Livraison $livraison, string $statut, array $details): void
    {
        $sql = "INSERT INTO historique_suivi 
                (livraison_id, statut, description, localisation, date_event, email_envoye) 
                VALUES (:livraison_id, :statut, :description, :localisation, NOW(), TRUE)";

        $description = $details['description'] ?? $this->getDescriptionStatut($statut);
        $localisation = $details['localisation'] ?? null;

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->executeQuery([
            'livraison_id' => $livraison->getId(),
            'statut' => $statut,
            'description' => $description,
            'localisation' => $localisation
        ]);
    }

    /**
     * Envoie une newsletter automatique de suivi
     */
    private function envoyerNewsletterSuivi(Livraison $livraison, string $statut): void
    {
        $commande = $livraison->getCommande();
        $email = $commande->getEmailClient();

        if (!$email) {
            return;
        }

        $sujet = $this->getSujetNewsletter($statut);
        $contenu = $this->genererContenuNewsletter($livraison, $statut);

        // Enregistrer dans la table newsletter_suivi
        $sql = "INSERT INTO newsletter_suivi 
                (livraison_id, email_destinataire, sujet_template, contenu_html, statut) 
                VALUES (:livraison_id, :email, :sujet, :contenu, 'envoye')";

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->executeQuery([
            'livraison_id' => $livraison->getId(),
            'email' => $email,
            'sujet' => $sujet,
            'contenu' => $contenu
        ]);

        $this->logger->info('Newsletter suivi envoyée', [
            'livraison_id' => $livraison->getId(),
            'email' => $email,
            'statut' => $statut
        ]);
    }

    /**
     * Génère le contenu HTML de la newsletter
     */
    private function genererContenuNewsletter(Livraison $livraison, string $statut): string
    {
        $commande = $livraison->getCommande();
        $numeroCommande = $commande->getNumeroCommande();
        $numeroSuivi = $livraison->getNumeroSuivi() ?? 'En attente';

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ffd700, #ffed4e); padding: 30px; text-align: center; }
                .header h1 { color: #1a1a2e; margin: 0; }
                .content { background: #f9f9f9; padding: 30px; }
                .status-badge { display: inline-block; padding: 10px 20px; background: #4CAF50; color: white; border-radius: 5px; font-weight: bold; }
                .info-box { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #ffd700; }
                .info-box strong { color: #1a1a2e; }
                .timeline { margin: 20px 0; }
                .timeline-item { padding: 15px; margin: 10px 0; background: white; border-left: 3px solid #4CAF50; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>📦 Mise à jour de votre livraison</h1>
                </div>
                <div class='content'>
                    <p>Bonjour,</p>
                    <p>Nous avons une mise à jour concernant votre commande <strong>{$numeroCommande}</strong>.</p>
                    
                    <div class='info-box'>
                        <strong>Statut actuel:</strong> <span class='status-badge'>{$this->getLibelleStatut($statut)}</span>
                    </div>
                    
                    <div class='info-box'>
                        <strong>Numéro de suivi:</strong> {$numeroSuivi}<br>
                        <strong>Transporteur:</strong> {$livraison->getTransporteur()}<br>
                        <strong>Type d'envoi:</strong> {$this->getLibelleTypeEnvoi($livraison->getTypeEnvoi())}
                    </div>
                    
                    " . $this->getMessageSpecifiqueStatut($statut, $livraison) . "
                    
                    <p><a href='#' style='display: inline-block; padding: 12px 30px; background: #ffd700; color: #1a1a2e; text-decoration: none; border-radius: 5px; font-weight: bold;'>Suivre ma livraison</a></p>
                </div>
                <div class='footer'>
                    <p>Merci de votre confiance 🙏<br>
                    L'équipe AsArt'sDev<br>
                    <a href='mailto:asartsdev.contact@gmail.com'>asartsdev.contact@gmail.com</a></p>
                </div>
            </div>
        </body>
        </html>
        ";

        return $html;
    }

    // Méthodes utilitaires

    private function getTransporteur(string $typeEnvoi): string
    {
        $transporteurs = [
            'standard' => 'Colissimo',
            'express' => 'La Poste Express',
            'express_retour' => 'UPS',
            'dedicace' => 'Transporteur Spécial'
        ];

        return $transporteurs[$typeEnvoi] ?? 'Colissimo';
    }

    private function getDetailsLivraison(string $typeEnvoi, bool $dedicace): string
    {
        if ($dedicace) {
            return "Envoi avec dédicace personnalisée - Le livre sera envoyé à l'auteur pour signature, puis vous sera expédié avec suivi.";
        }

        $details = [
            'standard' => 'Envoi standard avec suivi - Livraison sous 5 jours ouvrés',
            'express' => 'Envoi express avec suivi - Livraison sous 2 jours ouvrés',
            'express_retour' => 'Envoi express avec retour possible - Livraison sous 24h',
            'dedicace' => 'Envoi spécial avec dédicace - Livraison sous 3-5 jours'
        ];

        return $details[$typeEnvoi] ?? $details['standard'];
    }

    private function getDescriptionStatut(string $statut): string
    {
        $descriptions = [
            'en_attente' => 'Commande reçue et en attente de traitement',
            'preparation' => 'Votre commande est en cours de préparation',
            'pret_expedition' => 'Votre colis est prêt et attend l\'enlèvement',
            'envoye' => 'Votre colis a été expédié',
            'en_transit' => 'Votre colis est en cours d\'acheminement',
            'livre' => 'Votre colis a été livré avec succès',
        ];

        return $descriptions[$statut] ?? 'Mise à jour du statut';
    }

    private function getSujetNewsletter(string $statut): string
    {
        $sujets = [
            'en_attente' => '📨 Commande reçue - AsArt\'sDev',
            'preparation' => '📦 Votre commande est en préparation',
            'envoye' => '🚚 Votre colis est parti !',
            'en_transit' => '🌍 Votre colis est en route',
            'livre' => '✅ Votre colis est arrivé !',
        ];

        return $sujets[$statut] ?? 'Mise à jour de votre livraison';
    }

    private function getLibelleStatut(string $statut): string
    {
        $libelles = [
            'en_attente' => 'En attente',
            'preparation' => 'En préparation',
            'pret_expedition' => 'Prêt pour expédition',
            'envoye' => 'Envoyé',
            'en_transit' => 'En transit',
            'livre' => 'Livré',
        ];

        return $libelles[$statut] ?? $statut;
    }

    private function getLibelleTypeEnvoi(string $type): string
    {
        $libelles = [
            'standard' => 'Standard (5 jours)',
            'express' => 'Express (2 jours)',
            'express_retour' => 'Express avec retour (24h)',
            'dedicace' => 'Avec dédicace personnalisée',
        ];

        return $libelles[$type] ?? $type;
    }

    private function getMessageSpecifiqueStatut(string $statut, Livraison $livraison): string
    {
        $messages = [
            'envoye' => "<p><strong>📮 Bonne nouvelle !</strong><br>Votre colis a été expédié aujourd'hui et devrait arriver le " . 
                        $livraison->getDateLivraisonEstimee()->format('d/m/Y') . ".</p>",
            'en_transit' => "<p><strong>🚚 En route !</strong><br>Votre colis est actuellement à : <strong>" . 
                            ($livraison->getDerniereLocalisation() ?? 'Centre de tri') . "</strong></p>",
            'livre' => "<p><strong>🎉 Félicitations !</strong><br>Votre colis a été livré avec succès. Nous espérons que vous apprécierez votre lecture !</p>",
        ];

        return $messages[$statut] ?? '';
    }
}
