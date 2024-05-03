<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @method string getUserIdentifier()
 * @method string hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword)
 * @method bool isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword)
 * @method bool needsRehash(PasswordAuthenticatedUserInterface $user)
 */
#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?Group $group = null;

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): Student
    {
        $this->group = $group;
        return $this;
    }

    public function getRoles()
    {
        $roles = [];
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_STUDENT';
        return array_unique($roles);
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
        // TODO: Implement @method string hashPassword(PasswordAuthenticatedUserInterface $user, string $plainPassword)
        // TODO: Implement @method bool isPasswordValid(PasswordAuthenticatedUserInterface $user, string $plainPassword)
        // TODO: Implement @method bool needsRehash(PasswordAuthenticatedUserInterface $user)
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->surnames;
    }


}
