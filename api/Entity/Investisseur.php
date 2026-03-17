<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvestisseurRepository")
 * @ORM\Table(name="investisseurs", indexes={
 *     @ORM\Index(name="idx_investisseur_token", columns={"token"}),
 *     @ORM\Index(name="idx_investisseur_valide", columns={"valide"})
 * })
 */
class Investisseur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $entreprise;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $valide = false;

    /**
        * @ORM\Column(type="string", length=64, unique=true, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $justificatif;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $strategie;

    /**
     * @ORM\Column(type="float", options={"default": 0})
     */
    private $iaScore = 0.0;

    /**
     * @ORM\Column(type="float", options={"default": 0})
     */
    private $totalScore = 0.0;

    /**
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    private $ipAccess;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $ndaSigne = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenExpire;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getEntreprise(): ?string { return $this->entreprise; }
    public function setEntreprise(string $entreprise): self { $this->entreprise = $entreprise; return $this; }

    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }

    public function isValide(): bool { return (bool) $this->valide; }
    public function setValide(bool $valide): self { $this->valide = $valide; return $this; }

    public function getToken(): ?string { return $this->token; }
    public function setToken(string $token): self { $this->token = $token; return $this; }

    public function getTokenExpire(): ?\DateTimeInterface { return $this->tokenExpire; }
    public function setTokenExpire(?\DateTimeInterface $date): self { $this->tokenExpire = $date; return $this; }

    public function isNdaSigne(): bool { return (bool) $this->ndaSigne; }
    public function setNdaSigne(bool $val): self { $this->ndaSigne = $val; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): self { $this->email = $email; return $this; }

    public function getJustificatif(): ?string { return $this->justificatif; }
    public function setJustificatif(?string $justificatif): self { $this->justificatif = $justificatif; return $this; }

    public function getStrategie(): ?string { return $this->strategie; }
    public function setStrategie(?string $strategie): self { $this->strategie = $strategie; return $this; }

    public function getIaScore(): float { return (float) $this->iaScore; }
    public function setIaScore(float $iaScore): self { $this->iaScore = $iaScore; return $this; }

    public function getTotalScore(): float { return (float) $this->totalScore; }
    public function setTotalScore(float $totalScore): self { $this->totalScore = $totalScore; return $this; }

    public function getIpAccess(): ?string { return $this->ipAccess; }
    public function setIpAccess(?string $ipAccess): self { $this->ipAccess = $ipAccess; return $this; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
}
