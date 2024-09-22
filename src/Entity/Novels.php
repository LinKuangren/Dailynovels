<?php

namespace App\Entity;

use App\Repository\NovelsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: NovelsRepository::class)]
#[UniqueEntity('title')]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks()]
class Novels
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 4, max: 255)]
    private ?string $title = null;

    #[Vich\UploadableField(mapping: 'image_novel', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(min: 2, max: 110)]
    private ?string $author = null;

    #[ORM\ManyToMany(targetEntity: Categories::class, inversedBy: 'novels')]
    private Collection $Categories;

    #[ORM\ManyToMany(targetEntity: Tags::class, inversedBy: 'novels')]
    private Collection $Tags;

    #[ORM\Column]
    private ?bool $Visibilitie = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $UpdatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'novels')]
    private ?Traducteurs $traducteurs = null;

    #[ORM\OneToMany(mappedBy: 'novels', targetEntity: Chapitres::class)]
    private Collection $chapitres;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoris')]
    private Collection $favoris;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(['en cours', 'terminé', 'abandonné'])]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: 'novels', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'novels', targetEntity: Rating::class, orphanRemoval: true)]
    private Collection $ratings;

    private ?float $average = null;

    public function __construct()
    {
        $this->Categories = new ArrayCollection();
        $this->Tags = new ArrayCollection();
        $this->CreatedAt = new \DateTimeImmutable;
        $this->UpdatedAt = new \DateTimeImmutable();
        $this->chapitres = new ArrayCollection();
        $this->favoris = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->UpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategories(): Collection
    {
        return $this->Categories;
    }

    public function addCategory(Categories $category): static
    {
        if (!$this->Categories->contains($category)) {
            $this->Categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Categories $category): static
    {
        $this->Categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Tags>
     */
    public function getTags(): Collection
    {
        return $this->Tags;
    }

    public function addTag(Tags $tag): static
    {
        if (!$this->Tags->contains($tag)) {
            $this->Tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tags $tag): static
    {
        $this->Tags->removeElement($tag);

        return $this;
    }

    public function isVisibilitie(): ?bool
    {
        return $this->Visibilitie;
    }

    public function setVisibilitie(bool $Visibilitie): static
    {
        $this->Visibilitie = $Visibilitie;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->UpdatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $UpdatedAt): self
    {
        $this->UpdatedAt = $UpdatedAt;

        return $this;
    }

    public function getTraducteurs(): ?Traducteurs
    {
        return $this->traducteurs;
    }

    public function setTraducteurs(?Traducteurs $traducteurs): static
    {
        $this->traducteurs = $traducteurs;

        return $this;
    }

    /**
     * @return Collection<int, Chapitres>
     */
    public function getChapitres(): Collection
    {
        return $this->chapitres;
    }

    public function addChapitre(Chapitres $chapitre): static
    {
        if (!$this->chapitres->contains($chapitre)) {
            $this->chapitres->add($chapitre);
            $chapitre->setNovels($this);
        }

        return $this;
    }

    public function removeChapitre(Chapitres $chapitre): static
    {
        if ($this->chapitres->removeElement($chapitre)) {
            // set the owning side to null (unless already changed)
            if ($chapitre->getNovels() === $this) {
                $chapitre->setNovels(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setDefaultImageIfEmpty()
    {
        if (empty($this->imageName)) {
            $this->imageName = 'defaut.svg';
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(User $favori): static
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris->add($favori);
            $favori->addFavori($this);
        }

        return $this;
    }

    public function removeFavori(User $favori): static
    {
        if ($this->favoris->removeElement($favori)) {
            $favori->removeFavori($this);
        }

        return $this;
    }

    /**
     * Obtenir le nombre d'utilisateurs qui ont marqué la novel en favoris.
     */
    public function getNumberOfFavorites(): int
    {
        return $this->favoris->count();
    }

    /**
     * Obtenir le nombre de chapitre de la novel .
     */
    public function getNumberOfChapitres(): int
    {
        return $this->chapitres->count();
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setNovels($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getNovels() === $this) {
                $comment->setNovels(null);
            }
        }

        return $this;
    }

     /**
     * Obtenir le nombre de commentaire de la novel.
     */
    public function getNumberOfComments(): int
    {
        return $this->comments->count();
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setNovels($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getNovels() === $this) {
                $rating->setNovels(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of average
     */
    public function getAverage()
    {
        $ratings = $this->ratings;

        if ($ratings->toArray() === []) {
            $this->average = null;
            return $this->average;
        }

        $total = 0;
        foreach ($ratings as $rating) {
            $total += $rating->getRating();
        }

        $this->average = $total / count($ratings);

        return $this->average;
    }

}
