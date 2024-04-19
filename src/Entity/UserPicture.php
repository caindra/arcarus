<?php

namespace App\Entity;

use App\Repository\UserPictureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPictureRepository::class)]
class UserPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $binaryCode = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'picture', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBinaryCode(): ?string
    {
        return $this->binaryCode;
    }

    public function setBinaryCode(?string $binaryCode): UserPicture
    {
        $this->binaryCode = $binaryCode;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): UserPicture
    {
        $this->description = $description;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getPicture() !== $this) {
            $user->setPicture($this);
        }

        $this->user = $user;

        return $this;
    }
}
