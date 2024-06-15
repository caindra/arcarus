<?php

namespace App\Entity;

use App\Repository\SectionContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SectionContentRepository::class)]
class SectionContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'sectionContents')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Section $section = null;

    #[ORM\ManyToOne(inversedBy: 'sectionContents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassPicture $classPicture = null;

    #[ORM\OneToMany(targetEntity: UserSectionContent::class, mappedBy: 'sectionContent', cascade: ['persist', 'remove'])]
    private Collection $userContents;

    public function __construct()
    {
        $this->userContents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): SectionContent
    {
        $this->title = $title;
        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): SectionContent
    {
        $this->section = $section;
        return $this;
    }

    public function getClassPicture(): ?ClassPicture
    {
        return $this->classPicture;
    }

    public function setClassPicture(?ClassPicture $classPicture): SectionContent
    {
        $this->classPicture = $classPicture;
        return $this;
    }

    /**
     * @return Collection<int, UserSectionContent>
     */
    public function getUserContents(): Collection
    {
        return $this->userContents;
    }

    public function addUserContent(UserSectionContent $userContent): static
    {
        if (!$this->userContents->contains($userContent)) {
            $this->userContents->add($userContent);
            $userContent->setSectionContent($this);
        }

        return $this;
    }

    public function removeUserContent(UserSectionContent $userContent): static
    {
        if ($this->userContents->removeElement($userContent)) {
            // set the owning side to null (unless already changed)
            if ($userContent->getSectionContent() === $this) {
                $userContent->setSectionContent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->title ?? 'no title';
    }

}
