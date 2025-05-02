<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le texte de l'option est obligatoire")]
    #[Assert\Length(max: 255, maxMessage: "Le texte ne peut pas dépasser {{ limit }} caractères")]
    private ?string $text = null;

    #[ORM\Column]
    private ?bool $is_correct = false;
    
    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\ManyToOne(inversedBy: 'options')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Question $question = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function isIsCorrect(): ?bool
    {
        return $this->is_correct;
    }

    public function setIsCorrect(bool $is_correct): static
    {
        $this->is_correct = $is_correct;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

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
