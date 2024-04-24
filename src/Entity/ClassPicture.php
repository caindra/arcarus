<?php

namespace App\Entity;

use App\Repository\ClassPictureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassPictureRepository::class)]
class ClassPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $decoration = null;

    #[ORM\OneToMany(targetEntity: SectionContent::class, mappedBy: 'classPicture')]
    private Collection $sectionContents;

    #[ORM\OneToOne(mappedBy: 'classPicture', cascade: ['persist', 'remove'])]
    private ?Grade $grade = null;

    public function __construct()
    {
        $this->sectionContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDecoration(): ?string
    {
        return $this->decoration;
    }

    public function setDecoration(?string $decoration): ClassPicture
    {
        $this->decoration = $decoration;
        return $this;
    }

    /**
     * @return Collection<int, SectionContent>
     */
    public function getSectionContents(): Collection
    {
        return $this->sectionContents;
    }

    public function addSectionContent(SectionContent $sectionContent): static
    {
        if (!$this->sectionContents->contains($sectionContent)) {
            $this->sectionContents->add($sectionContent);
            $sectionContent->setClassPicture($this);
        }

        return $this;
    }

    public function removeSectionContent(SectionContent $sectionContent): static
    {
        if ($this->sectionContents->removeElement($sectionContent)) {
            // set the owning side to null (unless already changed)
            if ($sectionContent->getClassPicture() === $this) {
                $sectionContent->setClassPicture(null);
            }
        }

        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): static
    {
        // unset the owning side of the relation if necessary
        if ($grade === null && $this->grade !== null) {
            $this->grade->setClassPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($grade !== null && $grade->getClassPicture() !== $this) {
            $grade->setClassPicture($this);
        }

        $this->grade = $grade;

        return $this;
    }

}
