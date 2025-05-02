<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'projet')]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_projet')]
    private ?int $id_projet = null;

    #[ORM\Column(name: 'nom_projet', length: 150)]
    #[Assert\NotBlank(message: "Le nom du projet est obligatoire")]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: "Le nom du projet doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le nom du projet ne peut pas dépasser {{ limit }} caractères"
    )]
    private string $nom_projet;

    #[ORM\Column(name: 'desc_projet', type: 'text', nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La description du projet ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $desc_projet;

    #[ORM\Column(name: 'statut_projet', length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Not Started', 'In Progress', 'Completed', 'On Hold'],
        message: "Le statut du projet doit être Not Started, In Progress, Completed ou On Hold"
    )]
    private ?string $statut_projet;

    #[ORM\Column(name: 'date_debut_projet', type: 'date', nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date de début doit être une date valide")]
    #[Assert\LessThanOrEqual(
        propertyPath: "date_fin_projet",
        message: "La date de début doit être antérieure à la date de fin"
    )]
    private ?\DateTimeInterface $date_debut_projet;

    #[ORM\Column(name: 'date_fin_projet', type: 'date', nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date de fin doit être une date valide")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "date_debut_projet",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private ?\DateTimeInterface $date_fin_projet;

    #[ORM\OneToMany(mappedBy: 'projet', targetEntity: Tache::class)]
    private Collection $taches;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
    }

    // Getters and setters for all properties
    public function getIdProjet(): ?int
    {
        return $this->id_projet;
    }

    public function getNomProjet(): string
    {
        return $this->nom_projet;
    }

    public function setNomProjet(string $nom_projet): self
    {
        $this->nom_projet = $nom_projet;
        return $this;
    }

    public function getDescProjet(): ?string
    {
        return $this->desc_projet;
    }

    public function setDescProjet(?string $desc_projet): self
    {
        $this->desc_projet = $desc_projet;
        return $this;
    }

    public function getStatutProjet(): ?string
    {
        return $this->statut_projet;
    }

    public function setStatutProjet(?string $statut_projet): self
    {
        $this->statut_projet = $statut_projet;
        return $this;
    }

    public function getDateDebutProjet(): ?\DateTimeInterface
    {
        return $this->date_debut_projet;
    }

    public function setDateDebutProjet(?\DateTimeInterface $date_debut_projet): self
    {
        $this->date_debut_projet = $date_debut_projet;
        return $this;
    }

    public function getDateFinProjet(): ?\DateTimeInterface
    {
        return $this->date_fin_projet;
    }

    public function setDateFinProjet(?\DateTimeInterface $date_fin_projet): self
    {
        $this->date_fin_projet = $date_fin_projet;
        return $this;
    }

    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setProjet($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            // set the owning side to null (unless already changed)
            if ($tache->getProjet() === $this) {
                $tache->setProjet(null);
            }
        }
        return $this;
    }
}
