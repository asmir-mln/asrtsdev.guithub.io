<?php
/* AsArt'sDev | Entity Client | Symfony Doctrine | Signature invisible | ASmir Milia */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClientRepository")
 * @ORM\Table(name="clients", indexes={
 *     @ORM\Index(name="idx_email", columns={"email"}),
 *     @ORM\Index(name="idx_type", columns={"type_client"}),
 *     @ORM\Index(name="idx_statut", columns={"statut"})
 * })
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom est requis")
     * @Assert\Length(min=2, minMessage="Le nom doit contenir au minimum 2 caractères")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le prénom est requis")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="L'email est requis")
     * @Assert\Email(message="Email invalide")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $pays = 'France';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $entreprise;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=50, options={"default":"particulier"})
     */
    private $typeClient = 'particulier';

    /**
     * @ORM\Column(type="string", length=50, options={"default":"prospect"})
     */
    private $statut = 'prospect';

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $interets = [];

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $documentsPath;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateModification;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDernierContact;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commande", mappedBy="client", cascade={"remove"})
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="client", cascade={"remove"})
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ActiviteClient", mappedBy="client", cascade={"remove"})
     */
    private $activites;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->activites = new ArrayCollection();
        $this->dateInscription = new \DateTime();
        $this->dateModification = new \DateTime();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): self { $this->adresse = $adresse; return $this; }

    public function getCodePostal(): ?string { return $this->codePostal; }
    public function setCodePostal(?string $codePostal): self { $this->codePostal = $codePostal; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }

    public function getPays(): ?string { return $this->pays; }
    public function setPays(?string $pays): self { $this->pays = $pays; return $this; }

    public function getEntreprise(): ?string { return $this->entreprise; }
    public function setEntreprise(?string $entreprise): self { $this->entreprise = $entreprise; return $this; }

    public function getFonction(): ?string { return $this->fonction; }
    public function setFonction(?string $fonction): self { $this->fonction = $fonction; return $this; }

    public function getTypeClient(): ?string { return $this->typeClient; }
    public function setTypeClient(string $typeClient): self { $this->typeClient = $typeClient; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }

    public function getInterets(): ?array { return $this->interets; }
    public function setInterets(?array $interets): self { $this->interets = $interets; return $this; }

    public function getDocumentsPath(): ?string { return $this->documentsPath; }
    public function setDocumentsPath(?string $documentsPath): self { $this->documentsPath = $documentsPath; return $this; }

    public function getDateInscription(): ?\DateTimeInterface { return $this->dateInscription; }
    public function setDateInscription(\DateTimeInterface $dateInscription): self { $this->dateInscription = $dateInscription; return $this; }

    public function getDateModification(): ?\DateTimeInterface { return $this->dateModification; }
    public function setDateModification(\DateTimeInterface $dateModification): self { $this->dateModification = $dateModification; return $this; }

    public function getDateDernierContact(): ?\DateTimeInterface { return $this->dateDernierContact; }
    public function setDateDernierContact(?\DateTimeInterface $dateDernierContact): self { $this->dateDernierContact = $dateDernierContact; return $this; }

    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getCommandes(): Collection { return $this->commandes; }
    public function addCommande(Commande $commande): self {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setClient($this);
        }
        return $this;
    }

    public function getDocuments(): Collection { return $this->documents; }
    public function addDocument(Document $document): self {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setClient($this);
        }
        return $this;
    }

    public function getActivites(): Collection { return $this->activites; }

    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }

    public function getAdresseComplete(): string {
        return trim(($this->adresse ?? '') . ' ' . ($this->codePostal ?? '') . ' ' . ($this->ville ?? ''));
    }
}
