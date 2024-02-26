<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le nom ne doit pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères.")]
    #[Assert\Type(type: "string", message: "Le nom doit être une chaîne de caractères.")]

    private ?string $nom = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message:"La description ne doit pas être vide.")]
    #[Assert\Length(min:10, maxMessage:"Description doit  dépasser {{ limit }} caractères.")]

    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Faut préciser le lieu ")]
    #[Assert\Length(max:255, maxMessage:"Lieu ne doit pas  dépasser {{ limit }} caractères.")]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_evenement = null;

    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'evenement' ,cascade: ["remove"])]
    private Collection $tickets;

    #[ORM\Column(length: 255)]
    private ?string $image_evenement = null;

    public function __construct()
    {
        $this->date_evenement = new \DateTime();
        $this->tickets = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDateEvenement(): ?\DateTimeInterface
    {
        return $this->date_evenement;
    }

    public function setDateEvenement(\DateTimeInterface $date_evenement): static
    {
        $this->date_evenement = $date_evenement;

        return $this;
    }

    public function setTickets(Collection $tickets): void
    {
        $this->tickets = $tickets;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setEvenement($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getEvenement() === $this) {
                $ticket->setEvenement(null);
            }
        }

        return $this;
    }

    public function getImageEvenement(): ?string
    {
        return $this->image_evenement;
    }

    public function setImageEvenement(string $image_evenement): self
    {
        $this->image_evenement = $image_evenement;

        return $this;
    }

}