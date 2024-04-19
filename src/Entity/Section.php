<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $height = null;

    #[ORM\Column]
    private ?float $width = null;

    #[ORM\Column]
    private ?int $maxRowNumber = null;

    #[ORM\Column]
    private ?int $maxColumNumber = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

    #[ORM\Column(length: 255)]
    private ?string $position = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function setWidth(float $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getMaxRowNumber(): ?int
    {
        return $this->maxRowNumber;
    }

    public function setMaxRowNumber(?int $maxRowNumber): Section
    {
        $this->maxRowNumber = $maxRowNumber;
        return $this;
    }

    public function getMaxColumNumber(): ?int
    {
        return $this->maxColumNumber;
    }

    public function setMaxColumNumber(?int $maxColumNumber): Section
    {
        $this->maxColumNumber = $maxColumNumber;
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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): Section
    {
        $this->position = $position;
        return $this;
    }

}
