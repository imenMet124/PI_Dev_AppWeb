<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomDep = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descDep = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDep(): ?string
    {
        return $this->nomDep;
    }

    public function setNomDep(string $nomDep): static
    {
        $this->nomDep = $nomDep;

        return $this;
    }

    public function getDescDep(): ?string
    {
        return $this->descDep;
    }

    public function setDescDep(?string $descDep): static
    {
        $this->descDep = $descDep;

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
}
