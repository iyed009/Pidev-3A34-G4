<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id] 
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'Le nom du produit ne peut pas être vide')]
    #[Assert\Length(
        min: 5,
        max: 200,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne doit pas faire plus de {{ limit }} caractères'
    )]
    private $name;
    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    #[Assert\Positive(message: "The quantity must be positive.")]
    #[Assert\LessThanOrEqual(10, message: "The quantity cannot be greater than 10.")]
    private $quantite;

    #[ORM\Column(type: Types::TEXT)]


    private ?string $description = null;

    #[ORM\Column(type: "integer")]

    private ?int $price = null;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[Assert\NotBlank(message: ' ne peut pas être vide')]
    private ?CategorieP $categorieP = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategorieP(): ?CategorieP
    {
        return $this->categorieP;
    }

    public function setCategorieP(?CategorieP $categorieP): static
    {
        $this->categorieP = $categorieP;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
