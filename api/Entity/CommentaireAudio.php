<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentaireAudioRepository")
 * @ORM\Table(name="commentaires_audio")
 */
class CommentaireAudio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Livre", inversedBy="commentaires_audio")
     * @ORM\JoinColumn(nullable=false)
     */
    private $livre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre du commentaire est obligatoire")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le fichier audio est obligatoire")
     */
    private $fichier_audio;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree_secondes;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $auteur_commentaire = 'Asmir Milianni';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $chapitre_reference;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $page_reference;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $type_commentaire = 'analyse';

    /**
     * @ORM\Column(type="boolean")
     */
    private $acces_premium = true;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_ecoutes = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $transcription;

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

    public function setDescription(string $description): self
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

    public function getDureeSecondes(): ?int
    {
        return $this->duree_secondes;
    }

    public function setDureeSecondes(int $duree_secondes): self
    {
        $this->duree_secondes = $duree_secondes;
        return $this;
    }

    public function getAuteurCommentaire(): ?string
    {
        return $this->auteur_commentaire;
    }

    public function setAuteurCommentaire(string $auteur_commentaire): self
    {
        $this->auteur_commentaire = $auteur_commentaire;
        return $this;
    }

    public function getChapitreReference(): ?string
    {
        return $this->chapitre_reference;
    }

    public function setChapitreReference(?string $chapitre_reference): self
    {
        $this->chapitre_reference = $chapitre_reference;
        return $this;
    }

    public function getPageReference(): ?int
    {
        return $this->page_reference;
    }

    public function setPageReference(?int $page_reference): self
    {
        $this->page_reference = $page_reference;
        return $this;
    }

    public function getTypeCommentaire(): ?string
    {
        return $this->type_commentaire;
    }

    public function setTypeCommentaire(string $type_commentaire): self
    {
        $this->type_commentaire = $type_commentaire;
        return $this;
    }

    public function isAccesPremium(): ?bool
    {
        return $this->acces_premium;
    }

    public function setAccesPremium(bool $acces_premium): self
    {
        $this->acces_premium = $acces_premium;
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

    public function getTranscription(): ?string
    {
        return $this->transcription;
    }

    public function setTranscription(?string $transcription): self
    {
        $this->transcription = $transcription;
        return $this;
    }

    /**
     * Retourne la durée formatée (MM:SS)
     */
    public function getDureeFormatee(): string
    {
        $minutes = floor($this->duree_secondes / 60);
        $secondes = $this->duree_secondes % 60;
        return sprintf('%02d:%02d', $minutes, $secondes);
    }

    /**
     * Retourne la référence complète (chapitre + page)
     */
    public function getReferenceComplete(): string
    {
        $ref = [];
        if ($this->chapitre_reference) {
            $ref[] = $this->chapitre_reference;
        }
        if ($this->page_reference) {
            $ref[] = 'p.' . $this->page_reference;
        }
        return implode(' - ', $ref) ?: 'Général';
    }
}
