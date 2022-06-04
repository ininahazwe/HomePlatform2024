<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AboutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AboutRepository::class)
 * @ApiResource(
 *     attributes={
        "order"={"createdAt":"DESC"}
 *     },
 *     paginationItemsPerPage=1,
 *     collectionOperations={
 *     "get" = {
            "normalization_context"={"groups"={"read:about"}}
 *        }
 *     },
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"read:full:about"}}
 *       }
 *     }
 * )
 */
class About
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:about"})
     */
    private ?string $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"titre"})
     * @Groups({"read:about"})
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:about"})
     */
    private ?string $intro;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read:about"})
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="about_images", cascade={"persist"})
     * @Groups({"read:about"})
     */
    private Collection $images;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $titre2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $accroche;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
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
            $image->setAboutImages($this);
        }

        return $this;
    }

    public function removeImage(File $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAboutImages() === $this) {
                $image->setAboutImages(null);
            }
        }

        return $this;
    }

    public function getTitre2(): ?string
    {
        return $this->titre2;
    }

    public function setTitre2(?string $titre2): self
    {
        $this->titre2 = $titre2;

        return $this;
    }

    public function getDescription2(): ?string
    {
        return $this->description2;
    }

    public function setDescription2(?string $description2): self
    {
        $this->description2 = $description2;

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

    public function getAccroche(): ?string
    {
        return $this->accroche;
    }

    public function setAccroche(?string $accroche): self
    {
        $this->accroche = $accroche;

        return $this;
    }
}
