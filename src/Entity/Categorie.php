<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiFilter;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:project"})
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
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="categorie", cascade={"persist"})
     */
    private Collection $logo;

    /**
     * @ORM\ManyToMany(targetEntity=Project::class, mappedBy="categorie")
     */
    private Collection $projects;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="categories")
     */
    private ?Dictionnaire $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $link;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $video;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $intro;

    public function __construct()
    {
        $this->logo = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->projects = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->nom;
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
    public function getLogo(): Collection
    {
        return $this->logo;
    }

    public function addLogo(File $logo): self
    {
        if (!$this->logo->contains($logo)) {
            $this->logo[] = $logo;
            $logo->setCategorie($this);
        }

        return $this;
    }

    public function removeLogo(File $logo): self
    {
        if ($this->logo->removeElement($logo)) {
            // set the owning side to null (unless already changed)
            if ($logo->getCategorie() === $this) {
                $logo->setCategorie(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return Collection
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addCategorie($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeCategorie($this);
        }

        return $this;
    }

    public function getType(): ?Dictionnaire
    {
        return $this->type;
    }

    public function setType(?Dictionnaire $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }
}
