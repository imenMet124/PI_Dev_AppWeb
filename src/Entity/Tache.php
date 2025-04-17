<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
#[ORM\Table(name: 'tache')]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_tache')]
    private ?int $id_tache = null;

    #[ORM\Column(name: 'titre_tache', length: 150)]
    #[Assert\NotBlank(message: "Le titre de la tâche est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $titre_tache;

    #[ORM\Column(name: 'desc_tache', type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $desc_tache = null;

    #[ORM\Column(name: 'priorite', length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Low', 'Medium', 'High'],
        message: "La priorité doit être Low, Medium ou High"
    )]
    private ?string $priorite = null;

    #[ORM\Column(name: 'statut_tache', length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Not Started', 'In Progress', 'Completed'],
        message: "Le statut doit être Not Started, In Progress ou Completed"
    )]
    private ?string $statut_tache = null;

    #[ORM\Column(name: 'deadline', type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date limite doit être une date valide")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date limite ne peut pas être dans le passé"
    )]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(name: 'progression', type: Types::FLOAT, nullable: true)]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "La progression doit être entre {{ min }}% et {{ max }}%"
    )]
    private ?float $progression = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(name: 'id_projet', referencedColumnName: 'id_projet')]
    #[Assert\NotNull(message: "Un projet doit être associé à la tâche")]
    private ?Projet $projet = null;

    #[ORM\OneToMany(mappedBy: 'tache', targetEntity: Affectation::class)]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
    }

    public function getIdTache(): ?int
    {
        return $this->id_tache;
    }

    public function getTitreTache(): string
    {
        return $this->titre_tache;
    }

    public function setTitreTache(string $titre_tache): static
    {
        $this->titre_tache = $titre_tache;
        return $this;
    }

    public function getDescTache(): ?string
    {
        return $this->desc_tache;
    }

    public function setDescTache(?string $desc_tache): static
    {
        $this->desc_tache = $desc_tache;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(?string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getStatutTache(): ?string
    {
        return $this->statut_tache;
    }

    public function setStatutTache(?string $statut_tache): static
    {
        $this->statut_tache = $statut_tache;
        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(?\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;
        return $this;
    }

    public function getProgression(): ?float
    {
        return $this->progression;
    }

    public function setProgression(?float $progression): static
    {
        $this->progression = $progression;
        return $this;
    }

    public function getProjet(): ?Projet
    {
        return $this->projet;
    }

    public function setProjet(?Projet $projet): static
    {
        $this->projet = $projet;
        return $this;
    }

    /**
     * @return Collection<int, Affectation>
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): static
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setTache($this);
        }
        return $this;
    }

    public function removeAffectation(Affectation $affectation): static
    {
        if ($this->affectations->removeElement($affectation)) {
            if ($affectation->getTache() === $this) {
                $affectation->setTache(null);
            }
        }
        return $this;
    }
}
