<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    use ResourceId;
    use Timestapable;

    const TYPE_AVATAR = 1;
    const TYPE_ILLUSTRATION = 4;
    const TYPE_LOGO = 5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nomFichier;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="logo")
     */
    private ?Categorie $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     */
    private ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="images")
     */
    private ?Project $project;

    /**
     * @ORM\ManyToOne(targetEntity=Edition::class, inversedBy="avatar")
     */
    private ?Edition $edition_avatar;

    /**
     * @ORM\ManyToOne(targetEntity=Edition::class, inversedBy="illustration")
     */
    private ?Edition $edition_illustration;

    /**
     * @ORM\ManyToOne(targetEntity=About::class, inversedBy="photos")
     */
    private ?About $about_photos;

    /**
     * @ORM\ManyToOne(targetEntity=Partner::class, inversedBy="logo")
     */
    private $partner;

    public function __construct() {
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

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): self
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getEditionAvatar(): ?Edition
    {
        return $this->edition_avatar;
    }

    public function setEditionAvatar(?Edition $edition_avatar): self
    {
        $this->edition_avatar = $edition_avatar;

        return $this;
    }

    public function getEditionIllustration(): ?Edition
    {
        return $this->edition_illustration;
    }

    public function setEditionIllustration(?Edition $edition_illustration): self
    {
        $this->edition_illustration = $edition_illustration;

        return $this;
    }

    public function getAboutPhotos(): ?About
    {
        return $this->about_photos;
    }

    public function setAboutPhotos(?About $about_photos): self
    {
        $this->about_photos = $about_photos;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner): self
    {
        $this->partner = $partner;

        return $this;
    }
}
