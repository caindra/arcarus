<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "username", type: "string")]
abstract class User implements UserInterface, UserPasswordHasherInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 255)]
    protected ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 255)]
    #[Assert\Regex(pattern: '/^[A-Za-záéíóúÁÉÍÓÚñÑ]+(?: [A-Za-záéíóúÁÉÍÓÚñÑ]+)?$/', message: 'Invalid name format')]
    protected ?string $surnames = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 255)]
    protected ?string $password = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    protected ?UserPicture $picture = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max: 255)]
    protected ?string $userName = null;

    #[ORM\ManyToOne(inversedBy: 'containedUsers')]
    protected ?UserSectionContent $userSectionContent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getSurnames(): ?string
    {
        return $this->surnames;
    }

    public function setSurnames(?string $surnames): User
    {
        $this->surnames = $surnames;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }

    public function getPicture(): ?UserPicture
    {
        return $this->picture;
    }

    public function setPicture(?UserPicture $picture): void
    {
        $this->picture = $picture;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(?string $userName): User
    {
        $this->userName = $userName;
        return $this;
    }

    public function getUserSectionContent(): ?UserSectionContent
    {
        return $this->userSectionContent;
    }

    public function setUserSectionContent(?UserSectionContent $userSectionContent): User
    {
        $this->userSectionContent = $userSectionContent;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->surnames;
    }

}
