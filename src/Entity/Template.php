<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $styleName = null;

    #[ORM\Column(type: 'blob')]
    /**
     * @var resource|null $layout
     */
    private $layout = null;

    #[ORM\ManyToOne(inversedBy: 'templates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $organization = null;

    #[ORM\OneToMany(targetEntity: Section::class, mappedBy: 'template', orphanRemoval: true)]
    private Collection $sections;

    #[ORM\OneToMany(targetEntity: ClassPicture::class, mappedBy: 'template')]
    private Collection $classPictures;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->classPictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStyleName(): ?string
    {
        return $this->styleName;
    }

    public function setStyleName(?string $styleName): Template
    {
        $this->styleName = $styleName;
        return $this;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout($layout): Template
    {
        $this->layout = $layout;
        return $this;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): Template
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->setTemplate($this);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            // set the owning side to null (unless already changed)
            if ($section->getTemplate() === $this) {
                $section->setTemplate(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->styleName . ' - ' . $this->layout;
    }

    /**
     * @return Collection<int, ClassPicture>
     */
    public function getClassPictures(): Collection
    {
        return $this->classPictures;
    }

    public function addClassPicture(ClassPicture $classPicture): static
    {
        if (!$this->classPictures->contains($classPicture)) {
            $this->classPictures->add($classPicture);
            $classPicture->setTemplate($this);
        }

        return $this;
    }

    public function removeClassPicture(ClassPicture $classPicture): static
    {
        if ($this->classPictures->removeElement($classPicture)) {
            // set the owning side to null (unless already changed)
            if ($classPicture->getTemplate() === $this) {
                $classPicture->setTemplate(null);
            }
        }

        return $this;
    }

}
