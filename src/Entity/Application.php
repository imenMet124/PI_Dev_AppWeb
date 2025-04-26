<?php

namespace App\Entity;

use App\Enum\ApplicationStatus;
use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Le candidat est obligatoire.")]
    #[Assert\Valid] // Validation en cascade de l'entité Candidat
    private ?Candidat $candidat = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L’offre d’emploi est obligatoire.")]
    private ?JobOffer $jobOffer = null;

    #[ORM\Column(type: 'string', enumType: ApplicationStatus::class)]
    #[Assert\NotNull(message: "Le statut est obligatoire.")]
    private ApplicationStatus $status;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 2000,
        maxMessage: "Le message ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $message = null;

    #[ORM\Column]

    private ?\DateTimeImmutable $submittedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le chemin du CV est trop long."
    )]
    private ?string $cvSnapshotPath = null;

    // -----------------------------
    // Getters / Setters
    // -----------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->candidat;
    }

    public function setCandidat(?Candidat $candidat): static
    {
        $this->candidat = $candidat;
        return $this;
    }

    public function getJobOffer(): ?JobOffer
    {
        return $this->jobOffer;
    }

    public function setJobOffer(?JobOffer $jobOffer): static
    {
        $this->jobOffer = $jobOffer;
        return $this;
    }

    public function getStatus(): ApplicationStatus
    {
        return $this->status;
    }

    public function setStatus(ApplicationStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function getCvSnapshotPath(): ?string
    {
        return $this->cvSnapshotPath;
    }

    public function setCvSnapshotPath(?string $cvSnapshotPath): static
    {
        $this->cvSnapshotPath = $cvSnapshotPath;
        return $this;
    }
}
