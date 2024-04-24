<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    #[ORM\ManyToOne(inversedBy: 'students')]
    private ?Grade $grade = null;

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): Student
    {
        $this->grade = $grade;
        return $this;
    }

}
