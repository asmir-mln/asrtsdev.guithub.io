<?php
/* AsArt'sDev | Entity Commande | Symfony Doctrine | Signature invisible | ASmir Milia */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 * @ORM\Table(name="commandes")
 */
class Commande
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=50, unique=true, nullable=true)
     */
    private $numeroCommande;

    /**
     * @ORM\Column(type="string", length=50, options={"default":"autre"})
     */
    private $typeProjet = 'autre';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $acompte;

    /**
     * @ORM\Column(type="string", length=3, options={"default":"EUR"})
     */
    private $devise = 'EUR';

    /**
     * @ORM\Column(type="string", length=50, options={"default":"devis"})
     */
    private $statut = 'devis';

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateModification;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateLivraisonPrevue;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateLivraisonReelle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="commande")
     */
    private $documents;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): self { $this->client = $client; return $this; }

    public function getNumeroCommande(): ?string { return $this->numeroCommande; }
    public function setNumeroCommande(?string $numeroCommande): self { $this->numeroCommande = $numeroCommande; return $this; }

    public function getTypeProjet(): ?string { return $this->typeProjet; }
    public function setTypeProjet(string $typeProjet): self { $this->typeProjet = $typeProjet; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getMontant() { return $this->montant; }
    public function setMontant($montant): self { $this->montant = $montant; return $this; }

    public function getAcompte() { return $this->acompte; }
    public function setAcompte($acompte): self { $this->acompte = $acompte; return $this; }

    public function getDevise(): ?string { return $this->devise; }
    public function setDevise(string $devise): self { $this->devise = $devise; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }

    public function getDateModification(): ?\DateTimeInterface { return $this->dateModification; }
    public function setDateModification(\DateTimeInterface $dateModification): self { $this->dateModification = $dateModification; return $this; }

    public function getDateLivraisonPrevue(): ?\DateTimeInterface { return $this->dateLivraisonPrevue; }
    public function setDateLivraisonPrevue(?\DateTimeInterface $dateLivraisonPrevue): self { $this->dateLivraisonPrevue = $dateLivraisonPrevue; return $this; }

    public function getDateLivraisonReelle(): ?\DateTimeInterface { return $this->dateLivraisonReelle; }
    public function setDateLivraisonReelle(?\DateTimeInterface $dateLivraisonReelle): self { $this->dateLivraisonReelle = $dateLivraisonReelle; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getDocuments(): Collection { return $this->documents; }
}
