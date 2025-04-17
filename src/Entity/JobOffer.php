<?php

namespace App\Entity;

use App\Repository\JobOfferRepository;
use App\Enum\ContractType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobOfferRepository::class)]
class JobOffer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre de l’offre est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description de l’offre est obligatoire.")]
    #[Assert\Length(min: 20, minMessage: "La description doit être plus détaillée.")]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le lieu ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $location = null;

    #[ORM\Column(type: 'string', enumType: ContractType::class)]
    #[Assert\NotNull(message: "Le type de contrat est obligatoire.")]
    private ContractType $contractType;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le salaire minimum est obligatoire.")]
    #[Assert\PositiveOrZero(message: "Le salaire minimum doit être un nombre positif.")]
    private ?int $salaryMin = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le salaire maximum est obligatoire.")]
    #[Assert\Positive(message: "Le salaire maximum doit être un nombre positif.")]
    #[Assert\Expression(
        "this.getSalaryMax() >= this.getSalaryMin()",
        message: "Le salaire maximum doit être supérieur ou égal au salaire minimum."
    )]
    private ?int $salaryMax = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le département est obligatoire.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom du département est trop long.")]
    private ?string $department = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Type(\DateTimeImmutable::class)]
    #[Assert\LessThanOrEqual("today", message: "La date ne peut pas être dans le futur.")]
    private ?\DateTimeImmutable $datetime_immutable = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le statut d’activation est obligatoire.")]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(targetEntity: Application::class, mappedBy: 'jobOffer')]
    private Collection $applications;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getContractType(): ContractType
    {
        return $this->contractType;
    }

    public function setContractType(ContractType $contractType): self
    {
        $this->contractType = $contractType;
        return $this;
    }

    public function getSalaryMin(): ?int
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(int $salaryMin): static
    {
        $this->salaryMin = $salaryMin;

        return $this;
    }

    public function getSalaryMax(): ?int
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(int $salaryMax): static
    {
        $this->salaryMax = $salaryMax;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(string $department): static
    {
        $this->department = $department;

        return $this;
    }

    public function getDatetimeImmutable(): ?\DateTimeImmutable
    {
        return $this->datetime_immutable;
    }

    public function setDatetimeImmutable(?\DateTimeImmutable $datetime_immutable): static
    {
        $this->datetime_immutable = $datetime_immutable;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setJobOffer($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->applications->removeElement($application)) {
            if ($application->getJobOffer() === $this) {
                $application->setJobOffer(null);
            }
        }

        return $this;
    }
}
