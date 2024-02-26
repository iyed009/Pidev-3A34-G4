<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"Le nom ne doit pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
   /* #[Assert\Email(message: 'L\'adresse email "{{ value }}" n\'est pas valide.')]*/
    private ?string $email = null;

    #[ORM\Column(type: Types::INTEGER)]
   /* #[Assert\Regex(
        pattern: '/^\d{8}$/',
        message: 'Le numéro de téléphone doit comporter 8 chiffres.'
    )]
    #[Assert\NotBlank(message: 'La numero ne peut pas être vide.')]*/
    private ?int $numTele = null;

    #[ORM\Column(length: 255)]
    private ?string $etat = 'NonTraité';

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date doit être égale ou postérieure à aujourd'hui."
    )]
    private ?\DateTimeInterface $date = null;



    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    private ?User $utilisateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message:"Le prenom ne doit pas être vide.")]
    #[Assert\Length(max:255, maxMessage:"Le prenom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $prenom = null;



    #[ORM\OneToOne(mappedBy: "idReclamation", cascade: ['remove'])]
    private ?Reponse $reponse = null;

    public function getReponse(): ?Reponse
    {
        return $this->reponse;
    }

    public function setReponse(?Reponse $reponse): void
    {
        $this->reponse = $reponse;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getNumTele(): ?int
    {
        return $this->numTele;
    }

    public function setNumTele(int $numTele): static
    {
        $this->numTele = $numTele;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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
    public function __construct()
    {
        // Set the default value for date to the current date and time
        $this->date = new \DateTime();
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }






}
