<?php

namespace App\Entity;

use App\Repository\UserSectionContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserSectionContentRepository::class)]
class UserSectionContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?int $orderNumber = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'userSectionContent')]
    private Collection $containedUsers;

    #[ORM\ManyToOne(inversedBy: 'userContents')]
    private ?SectionContent $sectionContent = null;

    public function __construct()
    {
        $this->containedUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): UserSectionContent
    {
        $this->description = $description;
        return $this;
    }

    public function getOrderNumber(): ?int
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(?int $orderNumber): UserSectionContent
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getContainedUsers(): Collection
    {
        return $this->containedUsers;
    }

    public function addContainedUser(User $containedUser): static
    {
        if (!$this->containedUsers->contains($containedUser)) {
            $this->containedUsers->add($containedUser);
            $containedUser->setUserSectionContent($this);
        }

        return $this;
    }

    public function removeContainedUser(User $containedUser): static
    {
        if ($this->containedUsers->removeElement($containedUser)) {
            // set the owning side to null (unless already changed)
            if ($containedUser->getUserSectionContent() === $this) {
                $containedUser->setUserSectionContent(null);
            }
        }

        return $this;
    }

    public function getSectionContent(): ?SectionContent
    {
        return $this->sectionContent;
    }

    public function setSectionContent(?SectionContent $sectionContent): UserSectionContent
    {
        $this->sectionContent = $sectionContent;
        return $this;
    }

    public function __toString(): string
    {
        return $this->description;
    }

}
