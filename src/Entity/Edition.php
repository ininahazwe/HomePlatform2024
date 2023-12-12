<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Repository\EditionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=EditionRepository::class)
 * @ApiResource(
 *     attributes={
        "order"={"createdAt":"DESC"}
 *     },
 *     paginationItemsPerPage=2,
 *     normalizationContext={"groups"={"read:edition"}},
 *     collectionOperations={"get"},
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"read:edition", "read:full:edition"}}
 *       }
 *     }
 * )
 * @ApiFilter(BooleanFilter::class, properties={"status"})
 * @ApiFilter(OrderFilter::class, properties={"ordre"}, arguments={"orderParameterName"="order"})
 */
class Edition
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:edition"})
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="edition_avatar", cascade={"persist"})
     * @Groups({"read:edition"})
     */
    private Collection $avatar;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="edition_illustration", cascade={"persist"})
     */
    private Collection $illustration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $video;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"nom"})
     * @Groups({"read:edition"})
     */
    private ?string $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:edition"})
     */
    private ?string $city;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $intro;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"read:edition"})
     */
    private ?int $ordre;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:edition"})
     */
    private ?string $year;

    public function __construct()
    {
        $this->avatar = new ArrayCollection();
        $this->illustration = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
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

    public function getSlug(): ?string
    {
        return $this->slug;
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
    public function getAvatar(): Collection
    {
        return $this->avatar;
    }

    public function addAvatar(File $avatar): self
    {
        if (!$this->avatar->contains($avatar)) {
            $this->avatar[] = $avatar;
            $avatar->setEditionAvatar($this);
        }

        return $this;
    }

    public function removeAvatar(File $avatar): self
    {
        if ($this->avatar->removeElement($avatar)) {
            // set the owning side to null (unless already changed)
            if ($avatar->getEditionAvatar() === $this) {
                $avatar->setEditionAvatar(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getIllustration(): Collection
    {
        return $this->illustration;
    }

    public function addIllustration(File $illustration): self
    {
        if (!$this->illustration->contains($illustration)) {
            $this->illustration[] = $illustration;
            $illustration->setEditionIllustration($this);
        }

        return $this;
    }

    public function removeIllustration(File $illustration): self
    {
        if ($this->illustration->removeElement($illustration)) {
            // set the owning side to null (unless already changed)
            if ($illustration->getEditionIllustration() === $this) {
                $illustration->setEditionIllustration(null);
            }
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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

        return $this;
    }
}
