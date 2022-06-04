<?php

namespace App\Entity;

use App\Repository\DictionnaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DictionnaireRepository::class)
 */
class Dictionnaire
{
    use ResourceId;
    use Timestapable;

    const TYPE_CATEGORIE = 'Project category';
    const TYPE_ACCORDION = 'Accordion';
    const NAME_SCHOOL = 'School name';
    const EDUCATION_LEVEL = 'Education level';


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     */
    private ?string $value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="type")
     */
    private Collection $categories;

    /**
     * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="school")
     */
    private Collection $profile_school;

    /**
     * @ORM\OneToMany(targetEntity=Profile::class, mappedBy="level")
     */
    private Collection $profile_level;

    public static function getTypeList(): array {
        return array(
            'Project category' => Dictionnaire::TYPE_CATEGORIE,
            'Accordion' => Dictionnaire::TYPE_ACCORDION,
            'School name' => Dictionnaire::NAME_SCHOOL,
            'Education level' => Dictionnaire::EDUCATION_LEVEL,
        );
    }

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->value;
    }


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->categories = new ArrayCollection();
        $this->profile_school = new ArrayCollection();
        $this->profile_level = new ArrayCollection();
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

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

    /**
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setType($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getType() === $this) {
                $category->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProfileSchool(): Collection
    {
        return $this->profile_school;
    }

    public function addProfileSchool(Profile $profileSchool): self
    {
        if (!$this->profile_school->contains($profileSchool)) {
            $this->profile_school[] = $profileSchool;
            $profileSchool->setSchool($this);
        }

        return $this;
    }

    public function removeProfileSchool(Profile $profileSchool): self
    {
        if ($this->profile_school->removeElement($profileSchool)) {
            // set the owning side to null (unless already changed)
            if ($profileSchool->getSchool() === $this) {
                $profileSchool->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProfileLevel(): Collection
    {
        return $this->profile_level;
    }

    public function addProfileLevel(Profile $profileLevel): self
    {
        if (!$this->profile_level->contains($profileLevel)) {
            $this->profile_level[] = $profileLevel;
            $profileLevel->setLevel($this);
        }

        return $this;
    }

    public function removeProfileLevel(Profile $profileLevel): self
    {
        if ($this->profile_level->removeElement($profileLevel)) {
            // set the owning side to null (unless already changed)
            if ($profileLevel->getLevel() === $this) {
                $profileLevel->setLevel(null);
            }
        }

        return $this;
    }
}
