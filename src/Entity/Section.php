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
    private ?int $rowNumber = null;

    #[ORM\Column]
    private ?int $columNumber = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

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

    public function getRowNumber(): ?int
    {
        return $this->rowNumber;
    }

    public function setRowNumber(int $rowNumber): static
    {
        $this->rowNumber = $rowNumber;

        return $this;
    }

    public function getColumNumber(): ?int
    {
        return $this->columNumber;
    }

    public function setColumNumber(int $columNumber): static
    {
        $this->columNumber = $columNumber;

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

}
