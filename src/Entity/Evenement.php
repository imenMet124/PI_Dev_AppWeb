<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom_Evenement = null;

    #[ORM\Column(length: 255)]
    private ?string $Description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $Heure = null;

    #[ORM\Column]
    private ?int $Capacite = null;

    #[ORM\Column(nullable: true)]
    private ?int $Nombre_Participants = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Image_Path = null;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Participation::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): static
    {
        $this->id = $Id;
        return $this;
    }

    public function getNomEvenement(): ?string
    {
        return $this->Nom_Evenement;
    }

    public function setNomEvenement(string $Nom_Evenement): static
    {
        $this->Nom_Evenement = $Nom_Evenement;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->Date;
    }

    public function setDate(\DateTimeInterface $Date): static
    {
        $this->Date = $Date;
        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->Heure;
    }

    public function setHeure(\DateTimeInterface $Heure): static
    {
        $this->Heure = $Heure;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->Capacite;
    }

    public function setCapacite(int $Capacite): static
    {
        $this->Capacite = $Capacite;
        return $this;
    }

    public function getNombreParticipants(): ?int
    {
        return $this->Nombre_Participants;
    }

    public function setNombreParticipants(?int $Nombre_Participants): static
    {
        $this->Nombre_Participants = $Nombre_Participants;
        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->Image_Path;
    }

    public function setImagePath(?string $Image_Path): static
    {
        $this->Image_Path = $Image_Path;
        return $this;
    }

    /** @return Collection<int, Participation> */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setEvenement($this);
        }
        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getEvenement() === $this) {
                $participation->setEvenement(null);
            }
        }
        return $this;
    }
}
