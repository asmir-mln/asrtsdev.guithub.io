<?php
/* AsArt'sDev | Entity ActiviteClient | Symfony Doctrine | Signature invisible | ASmir Milia */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActiviteClientRepository")
 * @ORM\Table(name="activites_clients")
 */
class ActiviteClient
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="activites")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $client;

    /**
     * @ORM\Column(type="string", length=50, options={"default":"modification"})
     */
    private $typeActivite = 'modification';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateActivite;

    public function __construct()
    {
        $this->dateActivite = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    
    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): self { $this->client = $client; return $this; }

    public function getTypeActivite(): ?string { return $this->typeActivite; }
    public function setTypeActivite(string $typeActivite): self { $this->typeActivite = $typeActivite; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getDateActivite(): ?\DateTimeInterface { return $this->dateActivite; }
    public function setDateActivite(\DateTimeInterface $dateActivite): self { $this->dateActivite = $dateActivite; return $this; }
}
