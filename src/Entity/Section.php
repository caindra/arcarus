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
    private ?int $height = null;

    #[ORM\Column]
    private ?int $width = null;

    #[ORM\Column]
    private ?int $maxColCount = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Template $template = null;

    #[ORM\Column]
    private ?int $positionTop = null;

    #[ORM\Column]
    private ?int $positionLeft = null;


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

    public function getMaxColCount(): ?int
    {
        return $this->maxColCount;
    }

    public function setMaxColCount(?int $maxColCount): Section
    {
        $this->maxColCount = $maxColCount;
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

}
