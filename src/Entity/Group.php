<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\ManyToOne(inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicYear $academicYear = null;

    #[ORM\OneToMany(targetEntity: Student::class, mappedBy: 'group', cascade: ['remove'])]
    private Collection $students;

    #[ORM\ManyToMany(targetEntity: Professor::class, inversedBy: 'groups', cascade: ['remove'])]
    private Collection $professors;

    #[ORM\OneToMany(targetEntity: Professor::class, mappedBy: 'mentoredClass', cascade: ['remove'])]
    private Collection $mentors;

    #[ORM\OneToOne(inversedBy: 'group', cascade: ['persist', 'remove'])]
    private ?ClassPicture $classPicture = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->professors = new ArrayCollection();
        $this->mentors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): Group
    {
        $this->organization = $organization;
        return $this;
    }

    public function getAcademicYear(): ?AcademicYear
    {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): Group
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
            $student->setGroup($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            // set the owning side to null (unless already changed)
            if ($student->getGroup() === $this) {
                $student->setGroup(null);
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

    /**
     * @return Collection<int, Professor>
     */
    public function getMentors(): Collection
    {
        return $this->mentors;
    }

    public function addMentor(Professor $mentor): static
    {
        if (!$this->mentors->contains($mentor)) {
            $this->mentors->add($mentor);
            $mentor->setMentoredClass($this);
        }

        return $this;
    }

    public function removeMentor(Professor $mentor): static
    {
        if ($this->mentors->removeElement($mentor)) {
            // set the owning side to null (unless already changed)
            if ($mentor->getMentoredClass() === $this) {
                $mentor->setMentoredClass(null);
            }
        }

        return $this;
    }

    public function getClassPicture(): ?ClassPicture
    {
        return $this->classPicture;
    }

    public function setClassPicture(?ClassPicture $classPicture): Group
    {
        $this->classPicture = $classPicture;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
