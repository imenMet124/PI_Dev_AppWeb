<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
#[ORM\Table(name: 'tache')]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_tache')]
    private ?int $id_tache = null;

    #[ORM\Column(name: 'titre_tache', length: 150)]
    private string $titre_tache;

    #[ORM\Column(name: 'desc_tache', type: Types::TEXT, nullable: true)]
    private ?string $desc_tache = null;

    #[ORM\Column(name: 'priorite', length: 50, nullable: true)]
    private ?string $priorite = null;

    #[ORM\Column(name: 'statut_tache', length: 50, nullable: true)]
    private ?string $statut_tache = null;

    #[ORM\Column(name: 'deadline', type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(name: 'progression', type: Types::FLOAT, nullable: true)]
    private ?float $progression = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(name: 'id_projet', referencedColumnName: 'id_projet')]
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
