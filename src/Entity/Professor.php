<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor extends User
{
    #[ORM\Column]
    private ?bool $isAdmin = false;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'professors')]
    private Collection $groups;

    #[ORM\ManyToOne(inversedBy: 'mentors')]
    private ?Group $mentoredClass = null;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addProfessor($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        if ($this->groups->removeElement($group)) {
            $group->removeProfessor($this);
        }

        return $this;
    }

    public function getMentoredClass(): ?Group
    {
        return $this->mentoredClass;
    }

    public function setMentoredClass(?Group $mentoredClass): Professor
    {
        $this->mentoredClass = $mentoredClass;
        return $this;
    }


}
