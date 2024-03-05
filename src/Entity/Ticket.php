<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le prix ne doit pas être vide. ")]
    #[Assert\GreaterThan(value: 0, message: "Le prix doit être supérieure à 0 !")]
    private ?int $prix = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Le type  ne doit pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le type ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"Le nbreTicket ne  doit pas être vide. ")]
    #[Assert\GreaterThan(value: 0, message: "Le nombre de ticket doit être supérieure à 0 !")]
    private ?int $nbreTicket = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    #[Assert\NotBlank(message:"Merci de saisir un évenement ")]
    private ?Evenement $evenement = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tickets')]
    #[ORM\JoinTable(name: "ticket_user")]
    private Collection $users;



    public function __construct()
    {
        $this->users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getNbreTicket(): ?int
    {
        return $this->nbreTicket;
    }

    public function setNbreTicket(int $nbreTicket): static
    {
        $this->nbreTicket = $nbreTicket;

        return $this;
    }

    public function getEvenement(): ?Evenement
    {
        return $this->evenement;
    }

    public function setEvenement(?Evenement $evenement): static
    {
        $this->evenement = $evenement;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }





}