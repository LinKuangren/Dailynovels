<?php

namespace App\Entity;

use App\Repository\TraducteursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TraducteursRepository::class)]
#[UniqueEntity('name')]
class Traducteurs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank()]
    #[Assert\Url]
    #[Assert\Length(min: 5, max: 255)]
    private ?string $link = null;

    #[ORM\OneToMany(mappedBy: 'traducteurs', targetEntity: Novels::class)]
    private Collection $novels;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    public function __construct()
    {
        $this->novels = new ArrayCollection();
        $this->CreatedAt = new \DateTimeImmutable;
        $this->UpdatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Novels>
     */
    public function getNovels(): Collection
    {
        return $this->novels;
    }

    public function addNovel(Novels $novel): static
    {
        if (!$this->novels->contains($novel)) {
            $this->novels->add($novel);
            $novel->setTraducteurs($this);
        }

        return $this;
    }

    public function removeNovel(Novels $novel): static
    {
        if ($this->novels->removeElement($novel)) {
            // set the owning side to null (unless already changed)
            if ($novel->getTraducteurs() === $this) {
                $novel->setTraducteurs(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->name;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $UpdatedAt): static
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    public function getNumberOfTraducteurs(): int
    {
        return $this->novels->count();
    }
}
