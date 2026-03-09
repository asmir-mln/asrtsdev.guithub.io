<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BandeAudioRepository")
 * @ORM\Table(name="bandes_audio")
 */
class BandeAudio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="bandes_audio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre de la piste audio est obligatoire")
     */
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le fichier audio est obligatoire")
     */
    private $fichier_audio;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $format = 'mp3';

    /**
     * @ORM\Column(type="integer")
     */
    private $duree_secondes;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre = 1;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type = 'lecture_integrale';

    /**
     * @ORM\Column(type="boolean")
     */
    private $acces_gratuit = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_ecoutes = 0;

    public function __construct()
    {
        $this->date_creation = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): self
    {
        $this->livre = $livre;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getFichierAudio(): ?string
    {
        return $this->fichier_audio;
    }

    public function setFichierAudio(string $fichier_audio): self
    {
        $this->fichier_audio = $fichier_audio;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getDureeSecondes(): ?int
    {
        return $this->duree_secondes;
    }

    public function setDureeSecondes(int $duree_secondes): self
    {
        $this->duree_secondes = $duree_secondes;
        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isAccesGratuit(): ?bool
    {
        return $this->acces_gratuit;
    }

    public function setAccesGratuit(bool $acces_gratuit): self
    {
        $this->acces_gratuit = $acces_gratuit;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function getNombreEcoutes(): ?int
    {
        return $this->nombre_ecoutes;
    }

    public function incrementerEcoutes(): self
    {
        $this->nombre_ecoutes++;
        return $this;
    }

    /**
     * Retourne la durée formatée (HH:MM:SS)
     */
    public function getDureeFormatee(): string
    {
        $heures = floor($this->duree_secondes / 3600);
        $minutes = floor(($this->duree_secondes % 3600) / 60);
        $secondes = $this->duree_secondes % 60;

        if ($heures > 0) {
            return sprintf('%02d:%02d:%02d', $heures, $minutes, $secondes);
        }
        return sprintf('%02d:%02d', $minutes, $secondes);
    }
}
