<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'iyedIdUser', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'iyedNomUser')]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true, name: 'iyedEmailUser')]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true, name: 'iyedPhoneUser')]
    private ?string $phone = null;

    #[ORM\Column(length: 255, name: 'iyedRoleUser')]
    private ?string $role = null;

    #[ORM\Column(length: 255, name: 'iyedPasswordUser')]
    private ?string $password = null;

    #[ORM\Column(length: 255, name: 'iyedPositionUser')]
    private ?string $position = null;

    #[ORM\Column(name: 'iyedSalaireUser')]
    private ?float $salary = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, name: 'iyedDateEmbaucheUser')]
    private ?\DateTimeInterface $hireDate = null;

    #[ORM\Column(length: 255, name: 'iyedStatutUser')]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'employees', fetch: 'EAGER')]
    #[ORM\JoinColumn(name: 'iyedIdDepUser', referencedColumnName: 'iyedIdDep', nullable: true)]
    private ?Department $department = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserPhoto $photo = null;

    #[ORM\OneToOne(mappedBy: 'manager', cascade: ['persist', 'remove'])]
    private ?Department $managedDepartment = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $todoistAccessToken = null;

    public function __construct()
    {
        $this->status = UserStatus::ACTIVE->value;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = [];
        if ($this->role) {
            $roles[] = 'ROLE_' . $this->role;
        }
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getSalary(): ?float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): static
    {
        $this->salary = $salary;
        return $this;
    }

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hireDate;
    }

    public function setHireDate(\DateTimeInterface $hireDate): static
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    public function getStatus(): UserStatus
    {
        return UserStatus::from($this->status);
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status->value;
        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        // Avoid circular reference by not adding this user to the department's employees
        $this->department = $department;
        return $this;
    }

    public function getPhoto(): ?UserPhoto
    {
        return $this->photo;
    }

    public function setPhoto(?UserPhoto $photo): static
    {
        if ($photo === null && $this->photo !== null) {
            $this->photo->setUser(null);
        }

        if ($photo !== null && $photo->getUser() !== $this) {
            $photo->setUser($this);
        }

        $this->photo = $photo;
        return $this;
    }

    public function __toString(): string
    {
        return $this->email ?? '';
    }

    public function getManagedDepartment(): ?Department
    {
        return $this->managedDepartment;
    }

    public function setManagedDepartment(?Department $managedDepartment): static
    {
        // Avoid circular reference by not setting the manager on the department
        $this->managedDepartment = $managedDepartment;
        return $this;
    }

    public function getTodoistAccessToken(): ?string
    {
        return $this->todoistAccessToken;
    }

    public function setTodoistAccessToken(?string $token): static
    {
        $this->todoistAccessToken = $token;
        return $this;
    }
}
