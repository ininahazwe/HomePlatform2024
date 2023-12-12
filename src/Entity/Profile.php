<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
class Profile
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="profile", cascade={"persist"})
     */
    private Collection $photo;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="profile_school")
     */
    private ?Dictionnaire $school;

    /**
     * @ORM\ManyToOne(targetEntity=Dictionnaire::class, inversedBy="profile_level")
     */
    private ?Dictionnaire $level;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $facebook;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $instagram;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $lindedin;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="profiles_skills")
     */
    private Collection $skills;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $degree;

    public function __construct()
    {
        $this->photo = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->skills = new ArrayCollection();
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
            $photo->setProfile($this);
        }

        return $this;
    }

    public function removePhoto(File $photo): self
    {
        if ($this->photo->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getProfile() === $this) {
                $photo->setProfile(null);
            }
        }

        return $this;
    }

    public function getSchool(): ?Dictionnaire
    {
        return $this->school;
    }

    public function setSchool(?Dictionnaire $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getLevel(): ?Dictionnaire
    {
        return $this->level;
    }

    public function setLevel(?Dictionnaire $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setProfile(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getProfile() !== $this) {
            $user->setProfile($this);
        }

        $this->user = $user;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getLindedin(): ?string
    {
        return $this->lindedin;
    }

    public function setLindedin(?string $lindedin): self
    {
        $this->lindedin = $lindedin;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Tag $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Tag $skill): self
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(?string $degree): self
    {
        $this->degree = $degree;

        return $this;
    }
}
