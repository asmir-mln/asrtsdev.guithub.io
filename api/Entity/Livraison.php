<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivraisonRepository")
 * @ORM\Table(name="livraisons")
 */
class Livraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Donation")
     * @ORM\JoinColumn(nullable=true)
     */
    private $donation;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $statut = 'en_attente';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $transporteur;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=true)
     */
    private $numero_suivi;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type_envoi = 'standard';

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $prix_base = 5.69;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $prix_livraison;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $prix_assurance = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $prix_total;

    /**
     * @ORM\Column(type="text")
     */
    private $adresse_livraison;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ville_livraison;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code_postal;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $pays_livraison = 'France';

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_preparation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_envoi;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_livraison_estimee;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_livraison_reelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dedicace_demandee = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $dedicace_texte;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $dedicace_montant_supplementaire = 0;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $derniere_mise_a_jour;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $derniere_localisation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes_internes;

    public function __construct()
    {
        $this->date_creation = new \DateTime();
        $this->derniere_mise_a_jour = new \DateTime();
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;
        return $this;
    }

    public function getDonation(): ?Donation
    {
        return $this->donation;
    }

    public function setDonation(?Donation $donation): self
    {
        $this->donation = $donation;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getTransporteur(): ?string
    {
        return $this->transporteur;
    }

    public function setTransporteur(?string $transporteur): self
    {
        $this->transporteur = $transporteur;
        return $this;
    }

    public function getNumeroSuivi(): ?string
    {
        return $this->numero_suivi;
    }

    public function setNumeroSuivi(?string $numero_suivi): self
    {
        $this->numero_suivi = $numero_suivi;
        return $this;
    }

    public function getTypeEnvoi(): ?string
    {
        return $this->type_envoi;
    }

    public function setTypeEnvoi(string $type_envoi): self
    {
        $this->type_envoi = $type_envoi;
        return $this;
    }

    public function getPrixBase(): ?float
    {
        return (float) $this->prix_base;
    }

    public function setPrixBase(float $prix_base): self
    {
        $this->prix_base = $prix_base;
        return $this;
    }

    public function getPrixLivraison(): ?float
    {
        return $this->prix_livraison ? (float) $this->prix_livraison : null;
    }

    public function setPrixLivraison(?float $prix_livraison): self
    {
        $this->prix_livraison = $prix_livraison;
        return $this;
    }

    public function getPrixAssurance(): ?float
    {
        return (float) $this->prix_assurance;
    }

    public function setPrixAssurance(float $prix_assurance): self
    {
        $this->prix_assurance = $prix_assurance;
        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prix_total ? (float) $this->prix_total : null;
    }

    public function setPrixTotal(?float $prix_total): self
    {
        $this->prix_total = $prix_total;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresse_livraison;
    }

    public function setAdresseLivraison(string $adresse_livraison): self
    {
        $this->adresse_livraison = $adresse_livraison;
        return $this;
    }

    public function getVilleLivraison(): ?string
    {
        return $this->ville_livraison;
    }

    public function setVilleLivraison(string $ville_livraison): self
    {
        $this->ville_livraison = $ville_livraison;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;
        return $this;
    }

    public function getPaysLivraison(): ?string
    {
        return $this->pays_livraison;
    }

    public function setPaysLivraison(string $pays_livraison): self
    {
        $this->pays_livraison = $pays_livraison;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function getDatePreparation(): ?\DateTimeInterface
    {
        return $this->date_preparation;
    }

    public function setDatePreparation(?\DateTimeInterface $date_preparation): self
    {
        $this->date_preparation = $date_preparation;
        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->date_envoi;
    }

    public function setDateEnvoi(?\DateTimeInterface $date_envoi): self
    {
        $this->date_envoi = $date_envoi;
        return $this;
    }

    public function getDateLivraisonEstimee(): ?\DateTimeInterface
    {
        return $this->date_livraison_estimee;
    }

    public function setDateLivraisonEstimee(?\DateTimeInterface $date_livraison_estimee): self
    {
        $this->date_livraison_estimee = $date_livraison_estimee;
        return $this;
    }

    public function getDateLivraisonReelle(): ?\DateTimeInterface
    {
        return $this->date_livraison_reelle;
    }

    public function setDateLivraisonReelle(?\DateTimeInterface $date_livraison_reelle): self
    {
        $this->date_livraison_reelle = $date_livraison_reelle;
        return $this;
    }

    public function isDedicaceDemandee(): ?bool
    {
        return $this->dedicace_demandee;
    }

    public function setDedicaceDemandee(bool $dedicace_demandee): self
    {
        $this->dedicace_demandee = $dedicace_demandee;
        return $this;
    }

    public function getDedicaceTexte(): ?string
    {
        return $this->dedicace_texte;
    }

    public function setDedicaceTexte(?string $dedicace_texte): self
    {
        $this->dedicace_texte = $dedicace_texte;
        return $this;
    }

    public function getDedicaceMontantSupplementaire(): ?float
    {
        return (float) $this->dedicace_montant_supplementaire;
    }

    public function setDedicaceMontantSupplementaire(float $dedicace_montant_supplementaire): self
    {
        $this->dedicace_montant_supplementaire = $dedicace_montant_supplementaire;
        return $this;
    }

    public function getDerniereMiseAJour(): ?\DateTimeInterface
    {
        return $this->derniere_mise_a_jour;
    }

    public function setDerniereMiseAJour(?\DateTimeInterface $derniere_mise_a_jour): self
    {
        $this->derniere_mise_a_jour = $derniere_mise_a_jour;
        return $this;
    }

    public function getDerniereLocalisation(): ?string
    {
        return $this->derniere_localisation;
    }

    public function setDerniereLocalisation(?string $derniere_localisation): self
    {
        $this->derniere_localisation = $derniere_localisation;
        return $this;
    }

    public function getNotesInternes(): ?string
    {
        return $this->notes_internes;
    }

    public function setNotesInternes(?string $notes_internes): self
    {
        $this->notes_internes = $notes_internes;
        return $this;
    }

    /**
     * Retourne le délai en jours avant livraison estimée
     */
    public function getDelaiRestant(): ?int
    {
        if (!$this->date_livraison_estimee) {
            return null;
        }

        $now = new \DateTime();
        $estimee = $this->date_livraison_estimee;
        $diff = $now->diff($estimee);

        return $diff->invert ? 0 : $diff->days;
    }

    /**
     * Vérifie si la livraison est en retard
     */
    public function estEnRetard(): bool
    {
        if (!$this->date_livraison_estimee || $this->statut === 'livre') {
            return false;
        }

        $now = new \DateTime();
        return $now > $this->date_livraison_estimee;
    }

    /**
     * Retourne le statut formaté pour affichage
     */
    public function getStatutLibelle(): string
    {
        $libelles = [
            'en_attente' => 'En attente',
            'preparation' => 'En préparation',
            'pret_expedition' => 'Prêt pour expédition',
            'envoye' => 'Envoyé',
            'en_transit' => 'En transit',
            'livre' => 'Livré',
            'retour_demande' => 'Retour demandé',
            'retour_en_cours' => 'Retour en cours',
            'retour_recu' => 'Retour reçu',
        ];

        return $libelles[$this->statut] ?? $this->statut;
    }
}
