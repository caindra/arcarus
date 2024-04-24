<?php

namespace App\Entity;

use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'grade')]
    private Collection $students;

    #[ORM\ManyToMany(targetEntity: Professor::class, inversedBy: 'grades')]
    private Collection $professors;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->professors = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->setGrade($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getGrade() === $this) {
                $student->setGrade(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Professor>
     */
    public function getProfessors(): Collection
    {
        return $this->professors;
    }

    public function addProfessor(Professor $professor): static
    {
        if (!$this->professors->contains($professor)) {
            $this->professors->add($professor);
        }

        return $this;
    }

    public function removeProfessor(Professor $professor): static
    {
        $this->professors->removeElement($professor);

        return $this;
    }

}
