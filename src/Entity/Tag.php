<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=TagRepository::class)
 */
class Tag
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
     * @ORM\ManyToMany(targetEntity=Project::class, inversedBy="tags")
     */
    private Collection $project;

    /**
     * @ORM\ManyToMany(targetEntity=Profile::class, mappedBy="skills")
     */
    private Collection $profiles_skills;

    public function __construct()
    {
        $this->project = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->profiles_skills = new ArrayCollection();
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

    /**
     * @return Collection
     */
    public function getProject(): Collection
    {
        return $this->project;
    }

    public function addProject(Project $project): self
    {
        if (!$this->project->contains($project)) {
            $this->project[] = $project;
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        $this->project->removeElement($project);

        return $this;
    }

    /**
     * @return Collection|Profile[]
     */
    public function getProfilesSkills(): Collection
    {
        return $this->profiles_skills;
    }

    public function addProfilesSkill(Profile $profilesSkill): self
    {
        if (!$this->profiles_skills->contains($profilesSkill)) {
            $this->profiles_skills[] = $profilesSkill;
            $profilesSkill->addSkill($this);
        }

        return $this;
    }

    public function removeProfilesSkill(Profile $profilesSkill): self
    {
        if ($this->profiles_skills->removeElement($profilesSkill)) {
            $profilesSkill->removeSkill($this);
        }

        return $this;
    }
}
