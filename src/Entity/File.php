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
    const TYPE_CV = 2;
    const TYPE_MOTIVATION = 3;
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
}
