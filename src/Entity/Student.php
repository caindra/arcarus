<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;

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

}
