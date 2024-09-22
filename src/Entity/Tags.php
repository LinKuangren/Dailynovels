<?php

namespace App\Entity;

use App\Repository\TagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TagsRepository::class)]
#[UniqueEntity('name')]
class Tags
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 4, max: 100)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Novels::class, mappedBy: 'Tags')]
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
            $novel->addTag($this);
        }

        return $this;
    }

    public function removeNovel(Novels $novel): static
    {
        if ($this->novels->removeElement($novel)) {
            $novel->removeTag($this);
        }

        return $this;
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
}
