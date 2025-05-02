<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'affectation')]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_affectation')]
    private ?int $id_affectation = null;

    #[ORM\Column(name: 'date_affectation', type: 'date', nullable: true)]
    #[Assert\Type("\DateTimeInterface", message: "La date d'affectation doit être une date valide")]
    #[Assert\LessThanOrEqual(
        "today",
        message: "La date d'affectation ne peut pas être dans le futur"
    )]
    private ?\DateTimeInterface $date_affectation;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_tache', referencedColumnName: 'id_tache')]
    #[Assert\NotNull(message: "Une tâche doit être associée à l'affectation")]
    private ?Tache $tache = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_emp', referencedColumnName: 'iyedIdUser')]
    #[Assert\NotNull(message: "Un employé doit être associé à l'affectation")]
    private ?User $employe = null;

    // Getters and setters for all properties

    public function getIdAffectation(): ?int
    {
        return $this->id_affectation;
    }

    public function getDateAffectation(): ?\DateTimeInterface
    {
        return $this->date_affectation;
    }

    public function setDateAffectation(?\DateTimeInterface $date_affectation): self
    {
        $this->date_affectation = $date_affectation;
        return $this;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): self
    {
        $this->tache = $tache;
        return $this;
    }

    public function getEmploye(): ?User
    {
        return $this->employe;
    }

    public function setEmploye(?User $employe): self
    {
        $this->employe = $employe;
        return $this;
    }
}
