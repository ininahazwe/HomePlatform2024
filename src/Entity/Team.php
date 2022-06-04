<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TeamRepository::class)
 * @ApiResource(
 *     attributes={
        "order"={"createdAt":"DESC"}
 *     },
 *     paginationItemsPerPage=4,
 *     normalizationContext={"groups"={"read:team"}},
 *     collectionOperations={"get"},
 *     itemOperations={
 *      "get"={
 *          "normalization_context"={"groups"={"read:team", "read:full:team"}}
 *       }
 *     }
 * )
 */
class Team
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:team"})
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"read:team"})
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $role;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="team", cascade={"persist"})
     * @Groups({"read:team"})
     */
    private Collection $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Slug(fields={"nom"})
     */
    private ?string $slug;

    public function __construct()
    {
        $this->photo = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPhoto(): Collection
    {
        return $this->photo;
    }

    public function addPhoto(File $photo): self
    {
        if (!$this->photo->contains($photo)) {
            $this->photo[] = $photo;
            $photo->setTeam($this);
        }

        return $this;
    }

    public function removePhoto(File $photo): self
    {
        if ($this->photo->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getTeam() === $this) {
                $photo->setTeam(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
