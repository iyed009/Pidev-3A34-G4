<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    // #[ORM\Id]
    // #[ORM\GeneratedValue('CUSTOM')]
    // #[ORM\Column(type: 'uuid', unique: true)]
    // #[ORM\CustomIdGenerator('doctrine.uuid_generator')]
    #[ORM\Id]
    #[ORM\Column(type: 'string', unique: true)]
    private ?string $id = null;


    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Nom is required")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Prenom is required")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\NotBlank(message: "Phone number is required")]
    #[Assert\Regex(
        pattern: "/^\d{8}$/",
        message: "Phone number must be exactly 8 digits."
    )]
    private ?int $numTele = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Length(
        min: 6,
        minMessage: "Password must be at least {{ limit }} characters long"
    )]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Adresse is required")]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?string $avatar;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull()]
    private \DateTimeImmutable $updatedAt;


    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'users')]
    private Collection $tickets;

    #[ORM\OneToMany(targetEntity: Reclamation::class, mappedBy: 'utilisateur')]
    private Collection $reclamations;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'utilisateur')]
    private Collection $reponses;

    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'utilisateur')]
    private Collection $salles;

    #[ORM\ManyToMany(targetEntity: Activite::class, mappedBy: 'reservation')]
    private Collection $activites;


    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    private $plainPassword;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->tickets = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->reponses = new ArrayCollection();
        $this->salles = new ArrayCollection();
        $this->activites = new ArrayCollection();
        $this->id = Uuid::v4()->toRfc4122();
    }



    // #[ORM\PrePersist]
    // public function prePersist(): void
    // {
    //     $this->avatar = 'https://avatars.dicebear.com/api/avataaars/' . urlencode($this->email) . '.svg';
    // }

    // #[ORM\PreUpdate]
    // public function preUpdate(): void
    // {
    //     $this->avatar = 'https://avatars.dicebear.com/api/avataaars/' . urlencode($this->email) . '.svg';
    //     $this->updatedAt = new \DateTimeImmutable();
    // }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getNumTele(): ?int
    {
        return $this->numTele;
    }

    public function setNumTele(int $numTele): self
    {
        $this->numTele = $numTele;

        return $this;
    }


    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }



    public function getUsername(): string
    {
        // This method can be kept for backward compatibility or if your application specifically requires it.
        return $this->getEmail();
    }

    public function getUserIdentifier(): string
    {
        // This is now the preferred method for identifying the user.
        return $this->getEmail();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }


    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }


    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }


    public function getSalt()
    {
        // Not needed for bcrypt or argon2i
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
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
            $ticket->addUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): static
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): static
    {
        if ($this->reclamations->removeElement($reclamation)) {
            // set the owning side to null (unless already changed)
            if ($reclamation->getUtilisateur() === $this) {
                $reclamation->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses->add($reponse);
            $reponse->setUtilisateur($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getUtilisateur() === $this) {
                $reponse->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            // set the owning side to null (unless already changed)
            if ($salle->getUtilisateur() === $this) {
                $salle->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activite>
     */
    public function getActivites(): Collection
    {
        return $this->activites;
    }

    public function addActivite(Activite $activite): static
    {
        if (!$this->activites->contains($activite)) {
            $this->activites->add($activite);
            $activite->addReservation($this);
        }

        return $this;
    }

    public function removeActivite(Activite $activite): static
    {
        if ($this->activites->removeElement($activite)) {
            $activite->removeReservation($this);
        }

        return $this;
    }


    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
