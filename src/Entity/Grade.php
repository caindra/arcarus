<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicYear $academicYear = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Grade
    {
        $this->name = $name;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): Grade
    {
        $this->organization = $organization;
        return $this;
    }

    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): Grade
    {
        $this->academicYear = $academicYear;
        return $this;
    }

}
