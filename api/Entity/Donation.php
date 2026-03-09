<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DonationRepository")
 * @ORM\Table(name="donations")
 */
class Donation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Commande", inversedBy="donations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $commande;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(message="Le montant est requis")
     * @Assert\Positive(message="Le montant doit être positif")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=3, options={"default":"EUR"})
     */
    private $devise = 'EUR';

    /**
     * Informations donateur - NULL = anonyme
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomDonateur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email(message="Email invalide")
     */
    private $emailDonateur;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephoneDonateur;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adresseDonateur;

    /**
     * Traçabilité
     * 
     * @ORM\Column(type="string", length=45)
     */
    private $ipAdresse;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $userAgent;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $dateDonation;

    /**
     * Gestion cadeaux/contreparties
     * 
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $eligibleCadeau = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cadeauId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cadeauEnvoye;

    /**
     * Code de suivi anonyme (UUID)
     * 
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $codeSuivi;

    /**
     * @ORM\Column(type="string", length=50, options={"default":"en_attente"})
     */
    private $statutSuivi = 'en_attente';

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    public function __construct()
    {
        $this->dateDonation = new \DateTime();
        $this->dateCreation = new \DateTime();
        $this->codeSuivi = strtoupper(bin2hex(random_bytes(16)));
    }

    // ==================== GETTERS & SETTERS ====================

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

    public function getMontant()
    {
        return $this->montant;
    }

    public function setMontant($montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;
        return $this;
    }

    // Donateur
    public function getNomDonateur(): ?string
    {
        return $this->nomDonateur;
    }

    public function setNomDonateur(?string $nom): self
    {
        $this->nomDonateur = $nom;
        return $this;
    }

    public function getEmailDonateur(): ?string
    {
        return $this->emailDonateur;
    }

    public function setEmailDonateur(?string $email): self
    {
        $this->emailDonateur = $email;
        return $this;
    }

    public function getTelephoneDonateur(): ?string
    {
        return $this->telephoneDonateur;
    }

    public function setTelephoneDonateur(?string $tel): self
    {
        $this->telephoneDonateur = $tel;
        return $this;
    }

    public function getAdresseDonateur(): ?string
    {
        return $this->adresseDonateur;
    }

    public function setAdresseDonateur(?string $adresse): self
    {
        $this->adresseDonateur = $adresse;
        return $this;
    }

    // Traçabilité
    public function getIpAdresse(): ?string
    {
        return $this->ipAdresse;
    }

    public function setIpAdresse(string $ip): self
    {
        $this->ipAdresse = $ip;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $ua): self
    {
        $this->userAgent = $ua;
        return $this;
    }

    public function getDateDonation(): ?\DateTime
    {
        return $this->dateDonation;
    }

    public function setDateDonation(\DateTime $date): self
    {
        $this->dateDonation = $date;
        return $this;
    }

    // Cadeaux
    public function isEligibleCadeau(): bool
    {
        return $this->eligibleCadeau;
    }

    public function setEligibleCadeau(bool $eligible): self
    {
        $this->eligibleCadeau = $eligible;
        return $this;
    }

    public function getCadeauId(): ?int
    {
        return $this->cadeauId;
    }

    public function setCadeauId(?int $id): self
    {
        $this->cadeauId = $id;
        return $this;
    }

    public function getCadeauEnvoye(): ?\DateTime
    {
        return $this->cadeauEnvoye;
    }

    public function setCadeauEnvoye(?\DateTime $date): self
    {
        $this->cadeauEnvoye = $date;
        return $this;
    }

    // Suivi
    public function getCodeSuivi(): ?string
    {
        return $this->codeSuivi;
    }

    public function setCodeSuivi(string $code): self
    {
        $this->codeSuivi = $code;
        return $this;
    }

    public function getStatutSuivi(): ?string
    {
        return $this->statutSuivi;
    }

    public function setStatutSuivi(string $statut): self
    {
        $this->statutSuivi = $statut;
        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $date): self
    {
        $this->dateCreation = $date;
        return $this;
    }

    // ==================== HELPERS ====================

    /**
     * Retourne le nom donateur ou "ANONYME"
     */
    public function getIdentificationDonateur(): string
    {
        return $this->nomDonateur ?? '🔒 ANONYME';
    }

    /**
     * Vérifie si la donation est anonyme
     */
    public function isAnonyme(): bool
    {
        return $this->nomDonateur === null;
    }

    /**
     * Statut du cadeau lisible
     */
    public function getStatutCadeauLisible(): string
    {
        if (!$this->eligibleCadeau) {
            return '❌ INELIGIBLE (Info incomplète)';
        }
        if ($this->cadeauEnvoye) {
            return '✅ ENVOYE le ' . $this->cadeauEnvoye->format('d/m/Y');
        }
        return '⏳ EN ATTENTE';
    }

    /**
     * Info complète pour affichage admin
     */
    public function getInfoAdmin(): string
    {
        if ($this->isAnonyme()) {
            return '🔒 ANONYME - Code: ' . $this->codeSuivi;
        }
        return $this->nomDonateur . ' (' . $this->emailDonateur . ')';
    }
}
