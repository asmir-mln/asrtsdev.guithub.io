<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LivreRepository")
 * @ORM\Table(name="livres")
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre est obligatoire")
     */
    private $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=1, max=3, message="Le numéro du livre doit être entre 1 et 3")
     */
    private $numero_livre;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $isbn;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     */
    private $nombre_pages;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $poids_kg;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_couverture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url_preview;

    /**
     * @ORM\Column(type="boolean")
     */
    private $disponible = true;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock_disponible = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_publication;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BandeAudio", mappedBy="livre", cascade={"persist", "remove"})
     */
    private $bandes_audio;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentaireAudio", mappedBy="livre", cascade={"persist", "remove"})
     */
    private $commentaires_audio;

    public function __construct()
    {
        $this->bandes_audio = new ArrayCollection();
        $this->commentaires_audio = new ArrayCollection();
        $this->date_creation = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumeroLivre(): ?int
    {
        return $this->numero_livre;
    }

    public function setNumeroLivre(int $numero_livre): self
    {
        $this->numero_livre = $numero_livre;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getPrix(): ?float
    {
        return (float) $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombre_pages;
    }

    public function setNombrePages(int $nombre_pages): self
    {
        $this->nombre_pages = $nombre_pages;
        return $this;
    }

    public function getPoidsKg(): ?float
    {
        return (float) $this->poids_kg;
    }

    public function setPoidsKg(float $poids_kg): self
    {
        $this->poids_kg = $poids_kg;
        return $this;
    }

    public function getImageCouverture(): ?string
    {
        return $this->image_couverture;
    }

    public function setImageCouverture(?string $image_couverture): self
    {
        $this->image_couverture = $image_couverture;
        return $this;
    }

    public function getUrlPreview(): ?string
    {
        return $this->url_preview;
    }

    public function setUrlPreview(?string $url_preview): self
    {
        $this->url_preview = $url_preview;
        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): self
    {
        $this->disponible = $disponible;
        return $this;
    }

    public function getStockDisponible(): ?int
    {
        return $this->stock_disponible;
    }

    public function setStockDisponible(int $stock_disponible): self
    {
        $this->stock_disponible = $stock_disponible;
        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeInterface $date_publication): self
    {
        $this->date_publication = $date_publication;
        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    /**
     * @return Collection|BandeAudio[]
     */
    public function getBandesAudio(): Collection
    {
        return $this->bandes_audio;
    }

    public function addBandeAudio(BandeAudio $bandeAudio): self
    {
        if (!$this->bandes_audio->contains($bandeAudio)) {
            $this->bandes_audio[] = $bandeAudio;
            $bandeAudio->setLivre($this);
        }
        return $this;
    }

    public function removeBandeAudio(BandeAudio $bandeAudio): self
    {
        if ($this->bandes_audio->removeElement($bandeAudio)) {
            if ($bandeAudio->getLivre() === $this) {
                $bandeAudio->setLivre(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection|CommentaireAudio[]
     */
    public function getCommentairesAudio(): Collection
    {
        return $this->commentaires_audio;
    }

    public function addCommentaireAudio(CommentaireAudio $commentaireAudio): self
    {
        if (!$this->commentaires_audio->contains($commentaireAudio)) {
            $this->commentaires_audio[] = $commentaireAudio;
            $commentaireAudio->setLivre($this);
        }
        return $this;
    }

    public function removeCommentaireAudio(CommentaireAudio $commentaireAudio): self
    {
        if ($this->commentaires_audio->removeElement($commentaireAudio)) {
            if ($commentaireAudio->getLivre() === $this) {
                $commentaireAudio->setLivre(null);
            }
        }
        return $this;
    }

    /**
     * Retourne la durée totale de toutes les bandes audio
     */
    public function getDureeTotaleAudio(): int
    {
        $total = 0;
        foreach ($this->bandes_audio as $bande) {
            $total += $bande->getDureeSecondes();
        }
        return $total;
    }

    /**
     * Retourne le nombre de commentaires audio
     */
    public function getNombreCommentairesAudio(): int
    {
        return $this->commentaires_audio->count();
    }
}
