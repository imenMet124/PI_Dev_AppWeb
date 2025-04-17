<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: CandidatRepository::class)]
class Candidat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le numéro de téléphone est obligatoire.")]
    #[Assert\Regex(pattern: "/^\+?[0-9\s\-]+$/", message: "Le numéro de téléphone n'est pas valide.")]
    #[Assert\Length(min: 8, max: 100, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.")]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: "L'adresse est trop longue.")]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull(message: "La date de naissance est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "La date est invalide.")]
    #[Assert\LessThan("today", message: "La date de naissance doit être dans le passé.")]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le chemin du CV est obligatoire.")]
    private ?string $resumePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverLetterPath = null;

    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["application/pdf"],
        mimeTypesMessage: "Le CV doit être au format PDF.",
        maxSizeMessage: "Le fichier CV ne doit pas dépasser 5 Mo."
    )]
    private ?UploadedFile $resumeFile = null;

    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["application/pdf"],
        mimeTypesMessage: "La lettre de motivation doit être au format PDF.",
        maxSizeMessage: "Le fichier lettre de motivation ne doit pas dépasser 5 Mo."
    )]
    private ?UploadedFile $coverLetterFile = null;

    #[ORM\Column(length: 25)]
    #[Assert\NotBlank(message: "Le lien LinkedIn est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le lien LinkedIn est trop long.")]
    #[Assert\Regex(
        pattern: "/^https?:\/\/(www\.)?linkedin\.com\/.*$/",
        message: "Le lien LinkedIn doit commencer par https://www.linkedin.com/"
    )]
    private ?string $linkedinUrl = null;

    // ---------------- Getters / Setters ----------------

    public function getId(): ?int { return $this->id; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): static { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): static { $this->lastName = $lastName; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(string $phone): static { $this->phone = $phone; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): static { $this->address = $address; return $this; }

    public function getDateOfBirth(): ?\DateTimeInterface { return $this->dateOfBirth; }
    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): static { $this->dateOfBirth = $dateOfBirth; return $this; }

    public function getResumePath(): ?string { return $this->resumePath; }
    public function setResumePath(string $resumePath): static { $this->resumePath = $resumePath; return $this; }

    public function getCoverLetterPath(): ?string { return $this->coverLetterPath; }
    public function setCoverLetterPath(?string $coverLetterPath): static { $this->coverLetterPath = $coverLetterPath; return $this; }

    public function getResumeFile(): ?UploadedFile { return $this->resumeFile; }
    public function setResumeFile(?UploadedFile $file): static { $this->resumeFile = $file; return $this; }

    public function getCoverLetterFile(): ?UploadedFile { return $this->coverLetterFile; }
    public function setCoverLetterFile(?UploadedFile $file): static { $this->coverLetterFile = $file; return $this; }

    public function getLinkedinUrl(): ?string { return $this->linkedinUrl; }
    public function setLinkedinUrl(string $linkedinUrl): static { $this->linkedinUrl = $linkedinUrl; return $this; }
}
