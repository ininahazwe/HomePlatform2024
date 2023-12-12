<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use ResourceId;
    use Timestapable;

    const GENRE_HOMME = 'Man';
    const GENRE_FEMME = 'Woman';
    const GENRE_AUTRE = 'Other';

    const ROLE_CANDIDAT = 'ROLE_CANDIDAT';

    const EN_ATTENTE = 0;
    const ACCEPTEE = 1;
    const REFUSEE = 2;

    const COMPTE_ACTIF = 3;
    const COMPTE_SUPPRIME = 4;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private string $password;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="user")
     */
    private Collection $files;

    /**
     * @ORM\ManyToMany(targetEntity=Project::class, mappedBy="auteur")
     */
    private Collection $projects;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $civilite;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, mappedBy="members")
     */
    private Collection $groups;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="editor", cascade={"persist"})
     */
    private Collection $project_editor;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isDeleted;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isMentor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $lastConnection;

    /**
     * @ORM\ManyToOne(targetEntity=Team::class, inversedBy="users")
     */
    private ?Team $team = null;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now');
        $this->groups = new ArrayCollection();
        $this->project_editor = new ArrayCollection();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string[]
     */
    public static function getGenreName(): array
    {
        return array(
            'Man' => User::GENRE_HOMME,
            'Woman' => User::GENRE_FEMME,
            'Other' => User::GENRE_AUTRE,
        );
    }

    /**
     * @return string[]
     */
    public static function getRoleNames(): array
    {
        return array(
            'Candidate' => User::ROLE_CANDIDAT,
        );
    }

    public function __toString(): string
    {
        return $this->nom;
    }


    /**
     * @return string
     */
    public function getFullname(): string
    {
        return $this->getPrenom().' '.$this->getNom();
    }

    public function checkRoles($role): bool
    {
        foreach($this->roles as $item)
        {
            if($item == $role)
            {
                return true;
            }
        }
        return false;
    }

    public function isCandidat(): bool
    {
        $role = "ROLE_CANDIDAT";
        return $this->checkRoles($role);
    }

    public function isMentor(): bool
    {
        $role = "ROLE_MENTOR";
        return $this->checkRoles($role);
    }

    public function isAdmin(): bool
    {
        $role = "ROLE_ADMIN";
        return $this->checkRoles($role);
    }

    public function isSuperAdmin(): bool
    {
        $role = "ROLE_SUPER_ADMIN";
        return $this->checkRoles($role);
    }

    public function getRoleName():string
    {
        if ($this->isSuperAdmin()){
            return 'Super Administrateur';
        }else if($this->isAdmin()){
            return 'Admin';
        }else if($this->isMentor()){
            return 'Mentor';
        }else if($this->isCandidat()){
            return 'Student';
        }else{
            return 'No info';
        }
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setUser($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getUser() === $this) {
                $file->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addAuteur($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeAuteur($this);
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
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

    public function getCivilite(): ?string
    {
        return $this->civilite;
    }

    public function setCivilite(?string $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addMember($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->removeElement($group)) {
            $group->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProjectEditor(): Collection
    {
        return $this->project_editor;
    }

    public function addProjectEditor(Project $projectEditor): self
    {
        if (!$this->project_editor->contains($projectEditor)) {
            $this->project_editor[] = $projectEditor;
            $projectEditor->setEditor($this);
        }

        return $this;
    }

    public function removeProjectEditor(Project $projectEditor): self
    {
        if ($this->project_editor->removeElement($projectEditor)) {
            // set the owning side to null (unless already changed)
            if ($projectEditor->getEditor() === $this) {
                $projectEditor->setEditor(null);
            }
        }

        return $this;
    }

    public function IsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSent(): Collection
    {
        return $this->sent;
    }

    public function addSent(Messages $sent): self
    {
        if (!$this->sent->contains($sent)) {
            $this->sent[] = $sent;
            $sent->setSender($this);
        }

        return $this;
    }

    public function removeSent(Messages $sent): self
    {
        if ($this->sent->removeElement($sent)) {
            // set the owning side to null (unless already changed)
            if ($sent->getSender() === $this) {
                $sent->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReceived(): Collection
    {
        return $this->received;
    }

    public function addReceived(Messages $received): self
    {
        if (!$this->received->contains($received)) {
            $this->received[] = $received;
            $received->setRecipient($this);
        }

        return $this;
    }

    public function removeReceived(Messages $received): self
    {
        if ($this->received->removeElement($received)) {
            // set the owning side to null (unless already changed)
            if ($received->getRecipient() === $this) {
                $received->setRecipient(null);
            }
        }

        return $this;
    }

    public function getIsMentor(): ?bool
    {
        return $this->isMentor;
    }

    public function setIsMentor(?bool $isMentor): self
    {
        $this->isMentor = $isMentor;

        return $this;
    }

    public function getAvatar(): string {
        $avatar = '';

        if (count($this->files) > 0){
            foreach ($this->files as $file){
                if ($file->getType() == 1 && $file->getProfile() == null){
                    $avatar = $file->getNom();
                }
            }
        }
        return $avatar;
    }

   /* public function hasGroup($user): bool {

        $result = false;

        if($user->getGroups()){
            $result = true;
        }

        return $result;
    }*/

   public function getLastConnection(): ?\DateTimeInterface
   {
       return $this->lastConnection;
   }

   public function setLastConnection(?\DateTimeInterface $lastConnection): self
   {
       $this->lastConnection = $lastConnection;

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
}
