<?php

namespace App\Entity;

use App\Enum\RoleUserEnum;
use App\Enum\StatutUserEnum;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'iyed_id_user')]
    private ?int $iyedIdUser = null;

    #[ORM\Column(name: 'iyed_nom_user', length: 255)]
    private string $iyedNomUser;

    #[ORM\Column(name: 'iyed_email_user', length: 255)]
    private string $iyedEmailUser;

    #[ORM\Column(name: 'iyed_phone_user', length: 20)]
    private string $iyedPhoneUser;

    #[ORM\Column(name: 'iyed_password_user', length: 255)]
    private string $iyedPasswordUser;

    #[ORM\Column(name: 'iyed_role_user', type: 'string', enumType: RoleUserEnum::class)]
    private RoleUserEnum $iyedRoleUser;

    #[ORM\Column(name: 'iyed_position_user', length: 255)]
    private string $iyedPositionUser;

    #[ORM\Column(name: 'iyed_salaire_user', type: 'decimal', precision: 10, scale: 2)]
    private float $iyedSalaireUser;

    #[ORM\Column(name: 'iyed_date_embauche_user', type: 'date')]
    private \DateTimeInterface $iyedDateEmbaucheUser;

    #[ORM\Column(name: 'iyed_statut_user', type: 'string', enumType: StatutUserEnum::class)]
    private StatutUserEnum $iyedStatutUser;

    #[ORM\Column(name: 'iyed_id_dep_user', nullable: true)]
    private ?int $iyedIdDepUser = null;

    #[ORM\OneToMany(mappedBy: 'employe', targetEntity: Affectation::class)]
    private Collection $affectations;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
    }

    // Getters and setters
    public function getIyedIdUser(): ?int
    {
        return $this->iyedIdUser;
    }

    public function getIyedNomUser(): string
    {
        return $this->iyedNomUser;
    }

    public function setIyedNomUser(string $iyedNomUser): self
    {
        $this->iyedNomUser = $iyedNomUser;
        return $this;
    }

    public function getIyedEmailUser(): string
    {
        return $this->iyedEmailUser;
    }

    public function setIyedEmailUser(string $iyedEmailUser): self
    {
        $this->iyedEmailUser = $iyedEmailUser;
        return $this;
    }

    public function getIyedPhoneUser(): string
    {
        return $this->iyedPhoneUser;
    }

    public function setIyedPhoneUser(string $iyedPhoneUser): self
    {
        $this->iyedPhoneUser = $iyedPhoneUser;
        return $this;
    }

    public function getIyedPasswordUser(): string
    {
        return $this->iyedPasswordUser;
    }

    public function setIyedPasswordUser(string $iyedPasswordUser): self
    {
        $this->iyedPasswordUser = $iyedPasswordUser;
        return $this;
    }

    public function getIyedRoleUser(): RoleUserEnum
    {
        return $this->iyedRoleUser;
    }

    public function setIyedRoleUser(RoleUserEnum $iyedRoleUser): self
    {
        $this->iyedRoleUser = $iyedRoleUser;
        return $this;
    }

    public function getIyedPositionUser(): string
    {
        return $this->iyedPositionUser;
    }

    public function setIyedPositionUser(string $iyedPositionUser): self
    {
        $this->iyedPositionUser = $iyedPositionUser;
        return $this;
    }

    public function getIyedSalaireUser(): float
    {
        return $this->iyedSalaireUser;
    }

    public function setIyedSalaireUser(float $iyedSalaireUser): self
    {
        $this->iyedSalaireUser = $iyedSalaireUser;
        return $this;
    }

    public function getIyedDateEmbaucheUser(): \DateTimeInterface
    {
        return $this->iyedDateEmbaucheUser;
    }

    public function setIyedDateEmbaucheUser(\DateTimeInterface $iyedDateEmbaucheUser): self
    {
        $this->iyedDateEmbaucheUser = $iyedDateEmbaucheUser;
        return $this;
    }

    public function getIyedStatutUser(): StatutUserEnum
    {
        return $this->iyedStatutUser;
    }

    public function setIyedStatutUser(StatutUserEnum $iyedStatutUser): self
    {
        $this->iyedStatutUser = $iyedStatutUser;
        return $this;
    }

    public function getIyedIdDepUser(): ?int
    {
        return $this->iyedIdDepUser;
    }

    public function setIyedIdDepUser(?int $iyedIdDepUser): self
    {
        $this->iyedIdDepUser = $iyedIdDepUser;
        return $this;
    }

    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations[] = $affectation;
            $affectation->setEmploye($this);
        }
        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->removeElement($affectation)) {
            if ($affectation->getEmploye() === $this) {
                $affectation->setEmploye(null);
            }
        }
        return $this;
    }
}
