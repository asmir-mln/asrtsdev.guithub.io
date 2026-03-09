<?php
/* AsArt'sDev | Entity Document | Symfony Doctrine | Signature invisible | ASmir Milia */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ORM\Table(name="documents")
 */
class Document
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="documents")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $commande;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomFichier;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $pathFichier;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $mimeType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tailleBytes;

    /**
     * @ORM\Column(type="string", length=50, options={"default":"autre"})
     */
    private $typeDocument = 'autre';

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpload;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateModification;

    public function __construct()
    {
        $this->dateUpload = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): self { $this->client = $client; return $this; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): self { $this->commande = $commande; return $this; }

    public function getNomFichier(): ?string { return $this->nomFichier; }
    public function setNomFichier(string $nomFichier): self { $this->nomFichier = $nomFichier; return $this; }

    public function getPathFichier(): ?string { return $this->pathFichier; }
    public function setPathFichier(string $pathFichier): self { $this->pathFichier = $pathFichier; return $this; }

    public function getMimeType(): ?string { return $this->mimeType; }
    public function setMimeType(?string $mimeType): self { $this->mimeType = $mimeType; return $this; }

    public function getTailleBytes(): ?int { return $this->tailleBytes; }
    public function setTailleBytes(?int $tailleBytes): self { $this->tailleBytes = $tailleBytes; return $this; }

    public function getTypeDocument(): ?string { return $this->typeDocument; }
    public function setTypeDocument(string $typeDocument): self { $this->typeDocument = $typeDocument; return $this; }

    public function getDateUpload(): ?\DateTimeInterface { return $this->dateUpload; }
    public function setDateUpload(\DateTimeInterface $dateUpload): self { $this->dateUpload = $dateUpload; return $this; }

    public function getDateModification(): ?\DateTimeInterface { return $this->dateModification; }
    public function setDateModification(\DateTimeInterface $dateModification): self { $this->dateModification = $dateModification; return $this; }
}
