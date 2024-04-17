<?php

namespace App\Entity;

use App\Repository\ClassPictureRepository;
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

}
