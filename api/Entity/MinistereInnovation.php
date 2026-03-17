<?php

namespace App\Entity;

use App\Repository\MinistereInnovationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MinistereInnovationRepository::class)]
class MinistereInnovation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ministere;

    #[ORM\Column(type: 'string', length: 255)]
    private string $innovation;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'float')]
    private float $cout;

    #[ORM\Column(type: 'float')]
    private float $marge;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int { return $this->id; }
    public function getMinistere(): string { return $this->ministere; }
    public function setMinistere(string $ministere): self { $this->ministere = $ministere; return $this; }
    public function getInnovation(): string { return $this->innovation; }
    public function setInnovation(string $innovation): self { $this->innovation = $innovation; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getCout(): float { return $this->cout; }
    public function setCout(float $cout): self { $this->cout = $cout; return $this; }
    public function getMarge(): float { return $this->marge; }
    public function setMarge(float $marge): self { $this->marge = $marge; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): self { $this->image = $image; return $this; }
}
