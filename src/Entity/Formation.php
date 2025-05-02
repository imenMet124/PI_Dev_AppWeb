<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(max: 255, maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $file_path = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name: "date_creation")]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToOne(mappedBy: 'formation', cascade: ['persist', 'remove'])]
    private ?Quiz $quiz = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(?string $file_path): static
    {
        $this->file_path = $file_path;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        // unset the owning side of the relation if necessary
        if ($quiz === null && $this->quiz !== null) {
            $this->quiz->setFormation(null);
        }

        // set the owning side of the relation if necessary
        if ($quiz !== null && $quiz->getFormation() !== $this) {
            $quiz->setFormation($this);
        }

        $this->quiz = $quiz;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function delete(): static
    {
        $this->deletedAt = new \DateTime();
        return $this;
    }

    public function restore(): static
    {
        $this->deletedAt = null;
        return $this;
    }
}
