<?php
// src/Entity/Product.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private $id;

    #[ORM\Column(type:"string", length:255)]
    private $name;

    #[ORM\Column(type:"text")]
    private $description;

    #[ORM\Column(type:"float")]
    private $price;

    #[ORM\Column(type:"datetime")]
    private $launchDate;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): self { $this->description = $description; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }
    public function getLaunchDate(): ?\DateTimeInterface { return $this->launchDate; }
    public function setLaunchDate(\DateTimeInterface $date): self { $this->launchDate = $date; return $this; }
}