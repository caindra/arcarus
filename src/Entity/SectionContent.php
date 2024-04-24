<?php

namespace App\Entity;

use App\Repository\SectionContentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionContentRepository::class)]
class SectionContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'sectionContents')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Section $section = null;

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

}
