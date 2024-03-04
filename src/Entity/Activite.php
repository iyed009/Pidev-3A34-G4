<?php

namespace App\Entity;

use App\Repository\ActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
USE Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActiviteRepository::class)]
class Activite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Nom manquant !")]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Nombre maximal manquant !")]
    #[Assert\Type(type: 'integer', message: "Le nombre maximal doit être un entier !")]
    #[Assert\GreaterThan(value: 0, message: "Le nombre maximal doit être supérieur à 0 !")]
    private ?int $nbrMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Nom du coach manquant !")]
    private ?string $coach = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Description manquante !")]
    #[Assert\Length(min: 15,exactMessage: "La description doit ètre supérieure à 15 caractère")]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'activite')]
    private ?Salle $salle = null;


    #[ORM\Column(length: 255)]
    private ?string $imageActivte = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'activites')]
    #[ORM\JoinTable(name: "activite_user")]
    private Collection $reservation;


    public function __construct()
    {
        $this->date = new \DateTime();
        $this->reservation = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getNbrMax(): ?int
    {
        return $this->nbrMax;
    }

    public function setNbrMax(int $nbrMax): static
    {
        $this->nbrMax = $nbrMax;

        return $this;
    }

    public function getCoach(): ?string
    {
        return $this->coach;
    }

    public function setCoach(string $coach): static
    {
        $this->coach = $coach;

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

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static
    {
        $this->salle = $salle;

        return $this;
    }

    public function setReservation(Collection $reservation): void
    {
        $this->reservation = $reservation;
    }


    public function getImageActivte(): ?string
    {
        return $this->imageActivte;
    }

    public function setImageActivte(string $imageActivte): static
    {
        $this->imageActivte = $imageActivte;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(User $reservation): static
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
        }

        return $this;
    }

    public function removeReservation(User $reservation): static
    {
        $this->reservation->removeElement($reservation);

        return $this;
    }

}
