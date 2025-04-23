<?php

namespace App\Entity;

use App\Repository\UserPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPhotoRepository::class)]
#[ORM\Table(name: 'userphoto')]
class UserPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'photo_path')]
    private ?string $photoPath = null;

    #[ORM\OneToOne(inversedBy: 'photo')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'iyedIdUser', nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotoPath(): ?string
    {
        return $this->photoPath;
    }

    public function setPhotoPath(string $photoPath): static
    {
        $this->photoPath = $photoPath;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
} 