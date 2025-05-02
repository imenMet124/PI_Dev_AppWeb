<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le texte de la question est obligatoire")]
    private ?string $text = null;

    #[ORM\ManyToOne(inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quiz $quiz = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deletedAt = null;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Option::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

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

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
            $option->setQuestion($this);
        }

        return $this;
    }

    public function removeOption(Option $option): static
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getQuestion() === $this) {
                $option->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * Check if at least one option is marked as correct
     */
    public function hasCorrectOption(): bool
    {
        foreach ($this->options as $option) {
            if ($option->isIsCorrect()) {
                return true;
            }
        }
        return false;
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
