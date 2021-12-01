<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"nom"})
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="project", cascade={"persist"})
     */
    private Collection $images;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="projects")
     */
    private Collection $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="projects")
     */
    private Collection $auteur;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="project")
     */
    private Collection $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $video;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isPublished;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $intro;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="project_avatar", cascade={"persist"})
     */
    private Collection $avatar;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->categorie = new ArrayCollection();
        $this->auteur = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->tags = new ArrayCollection();
        $this->avatar = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->nom;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(File $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProject($this);
        }

        return $this;
    }

    public function removeImage(File $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProject() === $this) {
                $image->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAuteur(): Collection
    {
        return $this->auteur;
    }

    public function addAuteur(User $auteur): self
    {
        if (!$this->auteur->contains($auteur)) {
            $this->auteur[] = $auteur;
        }

        return $this;
    }

    public function removeAuteur(User $auteur): self
    {
        $this->auteur->removeElement($auteur);

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addProject($this);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removeProject($this);
        }

        return $this;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(?string $video): self
    {
        $this->video = $video;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getAvatar(): Collection
    {
        return $this->avatar;
    }

    public function addAvatar(File $avatar): self
    {
        if (!$this->avatar->contains($avatar)) {
            $this->avatar[] = $avatar;
            $avatar->setProjectAvatar($this);
        }

        return $this;
    }

    public function removeAvatar(File $avatar): self
    {
        if ($this->avatar->removeElement($avatar)) {
            // set the owning side to null (unless already changed)
            if ($avatar->getProjectAvatar() === $this) {
                $avatar->setProjectAvatar(null);
            }
        }

        return $this;
    }
}
