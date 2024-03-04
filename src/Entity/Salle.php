<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
USE Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Nom manquant !")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Adresse manquante !")]
    private ?string $addresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Numéro de téléphone manquant !")]
    #[Assert\Type(type: 'integer', message: "Le numéro de téléphone doit être un entier !")]
    #[Assert\Length(exactly:8,exactMessage: "Le numéro de téléphone doit contenir exactement 8 chiffres." )]
    private ?int $numTel = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Capacité manquante !")]
    #[Assert\Type(type: 'integer', message: "La capacité doit être un entier !")]
    #[Assert\GreaterThan(value: 0, message: "La capacité doit être supérieure à 0 !")]
    private ?int $capacite = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "Description manquante !")]
    #[Assert\Length(min: 15,exactMessage: "La description doit ètre supérieure à 15 caractère")]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Activite::class, mappedBy: 'salle', cascade: ['remove'])]
    private Collection $activite;

    #[ORM\ManyToOne(inversedBy: 'salles')]
    private ?User $utilisateur = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Nombre d'abonné' manquant !")]
    #[Assert\Type(type: 'integer', message: "Le nombre de clients doit être un entier !")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le nombre de clients doit être supérieur ou égal à 0 !")]
    private ?int $nbrClient = null;

    #[ORM\Column(length: 255)]
    private ?string $logoSalle = null;


    public function __construct()
    {
        $this->activite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): static
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): static
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

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

    /**
     * @return Collection<int, Activite>
     */
    public function getActivite(): Collection
    {
        return $this->activite;
    }

    public function addActivite(Activite $activite): static
    {
        if (!$this->activite->contains($activite)) {
            $this->activite->add($activite);
            $activite->setSalle($this);
        }

        return $this;
    }

    public function removeActivite(Activite $activite): static
    {
        if ($this->activite->removeElement($activite)) {
            // set the owning side to null (unless already changed)
            if ($activite->getSalle() === $this) {
                $activite->setSalle(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?User
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?User $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getNbrClient(): ?int
    {
        return $this->nbrClient;
    }

    public function setNbrClient(int $nbrClient): static
    {
        $this->nbrClient = $nbrClient;

        return $this;
    }

    public function getLogoSalle(): ?string
    {
        return $this->logoSalle;
    }

    public function setLogoSalle(string $logoSalle): static
    {
        $this->logoSalle = $logoSalle;

        return $this;
    }
}
