<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReminderCommandeRepository")
 * @ORM\Table(name="reminder_commandes")
 */
class ReminderCommande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Commande", inversedBy="reminders")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $commande;

    /**
     * Type de reminder
     * 
     * @ORM\Column(type="string", length=50, options={"default":"creation"})
     */
    private $typeReminder = 'creation';

    /**
     * Message du reminder
     * 
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Le message est requis")
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", options={"default":"CURRENT_TIMESTAMP"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnvoi;

    /**
     * Statut: brouillon, envoye, non_applicable
     * 
     * @ORM\Column(type="string", length=50, options={"default":"brouillon"})
     */
    private $statut = 'brouillon';

    /**
     * Optionnel: email du destinataire
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emailDestinaire;

    /**
     * Optionnel: template utilisé
     * 
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $templateUsed;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
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

    public function getTypeReminder(): ?string
    {
        return $this->typeReminder;
    }

    public function setTypeReminder(string $type): self
    {
        $this->typeReminder = $type;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
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

    public function getDateEnvoi(): ?\DateTime
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(?\DateTime $date): self
    {
        $this->dateEnvoi = $date;
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

    public function getEmailDestinaire(): ?string
    {
        return $this->emailDestinaire;
    }

    public function setEmailDestinaire(?string $email): self
    {
        $this->emailDestinaire = $email;
        return $this;
    }

    public function getTemplateUsed(): ?string
    {
        return $this->templateUsed;
    }

    public function setTemplateUsed(?string $template): self
    {
        $this->templateUsed = $template;
        return $this;
    }

    // ==================== HELPERS ====================

    public function estEnvoye(): bool
    {
        return $this->statut === 'envoye' && $this->dateEnvoi !== null;
    }

    public function marquerEnvoye($email = null): self
    {
        $this->statut = 'envoye';
        $this->dateEnvoi = new \DateTime();
        if ($email) {
            $this->emailDestinaire = $email;
        }
        return $this;
    }

    public function getTypeRemainderLisible(): string
    {
        $types = [
            'creation' => '📝 Rappel de création',
            'paiement_recu' => '✅ Paiement reçu',
            'preparation' => '🎁 Préparation',
            'pret_expedition' => '📦 Prêt à partir',
            'envoye' => '🚚 Expédié',
            'recu' => '📬 Reçu',
            'relance' => '⏰ Relance',
        ];
        return $types[$this->typeReminder] ?? $this->typeReminder;
    }

    public function getIconeStatut(): string
    {
        return match($this->statut) {
            'envoye' => '✅',
            'brouillon' => '📝',
            'non_applicable' => '⏭️',
            default => '❓'
        };
    }
}
