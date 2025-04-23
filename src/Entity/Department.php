<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'iyedIdDep', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'iyedNomDep')]
    private ?string $name = null;

    #[ORM\Column(length: 1000, nullable: true, name: 'iyedDescriptionDep')]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true, name: 'iyedLocationDep')]
    private ?string $location = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'managedDepartment', fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'iyedManagerId', referencedColumnName: 'iyedIdUser', nullable: true)]
    private ?User $manager = null;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: User::class, fetch: 'LAZY')]
    private Collection $employees;

    public function __construct()
    {
        $this->employees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
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

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        // Avoid circular reference by not setting the managedDepartment on the user
        $this->manager = $manager;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getEmployees(): Collection
    {
        return $this->employees;
    }

    public function addEmployee(User $employee): static
    {
        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            // Avoid circular reference by not setting the department on the user
        }
        return $this;
    }

    public function removeEmployee(User $employee): static
    {
        $this->employees->removeElement($employee);
        // Avoid circular reference by not setting the department to null on the user
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
} 