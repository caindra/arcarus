<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $height = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    private ?int $width = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $maxColQuantity = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $positionTop = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $positionLeft = null;

    #[ORM\OneToMany(targetEntity: SectionContent::class, mappedBy: 'section', orphanRemoval: true)]
    private Collection $sectionContents;

    public function __construct()
    {
        $this->sectionContents = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): Section
    {
        $this->height = $height;
        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): Section
    {
        $this->width = $width;
        return $this;
    }

    public function getMaxColQuantity(): ?int
    {
        return $this->maxColQuantity;
    }

    public function setMaxColQuantity(?int $maxColQuantity): Section
    {
        $this->maxColQuantity = $maxColQuantity;
        return $this;
    }

    public function getPositionTop(): ?int
    {
        return $this->positionTop;
    }

    public function setPositionTop(?int $positionTop): Section
    {
        $this->positionTop = $positionTop;
        return $this;
    }

    public function getPositionLeft(): ?int
    {
        return $this->positionLeft;
    }

    public function setPositionLeft(?int $positionLeft): Section
    {
        $this->positionLeft = $positionLeft;
        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): Section
    {
        $this->template = $template;
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
            $sectionContent->setSection($this);
        }

        return $this;
    }

    public function removeSectionContent(SectionContent $sectionContent): static
    {
        if ($this->sectionContents->removeElement($sectionContent)) {
            // set the owning side to null (unless already changed)
            if ($sectionContent->getSection() === $this) {
                $sectionContent->setSection(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return 'Height: ' . $this->height . ', Width: ' . $this->width;
    }

}
