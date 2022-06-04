<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PartenairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PartenairesRepository::class)
 * @ApiResource(
 *     attributes={
        "order"={"createdAt":"DESC"}
 *     },
 *     paginationItemsPerPage=4,
 *     normalizationContext={"groups"={"read:partenaire"}},
 *     collectionOperations={"get"},
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"read:partenaire", "read:full:partenaire"}}
 *       }
 *     }
 * )
 */
class Partenaires
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:partenaire"})
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="partenaires", cascade={"persist"})
     * @Groups({"read:partenaire"})
     */
    private Collection $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"nom"})
     */
    private ?string $slug;

    public function __construct()
    {
        $this->logo = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
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
            $logo->setPartenaires($this);
        }

        return $this;
    }

    public function removeLogo(File $logo): self
    {
        if ($this->logo->removeElement($logo)) {
            // set the owning side to null (unless already changed)
            if ($logo->getPartenaires() === $this) {
                $logo->setPartenaires(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
}
