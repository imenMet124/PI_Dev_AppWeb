<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'affectation')]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_affectation')]
    private ?int $id_affectation = null;

    #[ORM\Column(name: 'date_affectation', type: 'date', nullable: true)]
    private ?\DateTimeInterface $date_affectation;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_tache', referencedColumnName: 'id_tache')]
    private ?Tache $tache = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'id_emp', referencedColumnName: 'iyed_id_user')]
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
