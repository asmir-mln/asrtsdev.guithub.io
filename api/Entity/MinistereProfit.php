<?php

namespace App\Entity;

use App\Repository\MinistereProfitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MinistereProfitRepository::class)]
class MinistereProfit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $ministere;

    #[ORM\Column(type: 'float')]
    private float $profit;

    public function getId(): ?int { return $this->id; }
    public function getMinistere(): string { return $this->ministere; }
    public function setMinistere(string $ministere): self { $this->ministere = $ministere; return $this; }
    public function getProfit(): float { return $this->profit; }
    public function setProfit(float $profit): self { $this->profit = $profit; return $this; }
}
