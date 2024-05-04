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
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: SectionContent::class, mappedBy: 'classPicture')]
    private Collection $sectionContents;

    #[ORM\OneToOne(mappedBy: 'classPicture', cascade: ['persist', 'remove'])]
    private ?Group $group = null;

    #[ORM\ManyToOne(inversedBy: 'classPictures')]
    private ?Template $template = null;

    public function __construct()
    {
        $this->sectionContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): ClassPicture
    {
        $this->description = $description;
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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): static
    {
        // unset the owning side of the relation if necessary
        if ($group === null && $this->group !== null) {
            $this->group->setClassPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($group !== null && $group->getClassPicture() !== $this) {
            $group->setClassPicture($this);
        }

        $this->group = $group;

        return $this;
    }

    public function __toString(): string
    {
        return $this->description;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): ClassPicture
    {
        $this->template = $template;
        return $this;
    }


}
