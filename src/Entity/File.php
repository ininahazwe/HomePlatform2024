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
     * @ORM\ManyToOne(targetEntity=Partenaires::class, inversedBy="logo")
     */
    private ?Partenaires $partenaires;

    /**
     * @ORM\ManyToOne(targetEntity=About::class, inversedBy="images")
     */
    private ?About $about_images;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="photo")
     */
    private ?Profile $profile;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="photo")
     */
    private ?Team $team;

    /**
     * @ORM\ManyToOne(targetEntity=Project::class, inversedBy="avatar")
     */
    private ?Project $project_avatar;

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

    public function getPartenaires(): ?Partenaires
    {
        return $this->partenaires;
    }

    public function setPartenaires(?Partenaires $partenaires): self
    {
        $this->partenaires = $partenaires;

        return $this;
    }

    public function getAboutImages(): ?About
    {
        return $this->about_images;
    }

    public function setAboutImages(?About $about_images): self
    {
        $this->about_images = $about_images;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getProjectAvatar(): ?Project
    {
        return $this->project_avatar;
    }

    public function setProjectAvatar(?Project $project_avatar): self
    {
        $this->project_avatar = $project_avatar;

        return $this;
    }
    public function __toString(): string {
        return $this->nom;
    }
}
