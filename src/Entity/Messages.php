<?php

namespace App\Entity;

use App\Data\ConvertDate;
use App\Repository\MessagesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessagesRepository::class)
 */
class Messages
{
    use ResourceId;
    use Timestapable;

    CONST TYPE_GROUP = 1;
    CONST TYPE_CHAT = 2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private int $is_read = 0;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $sender;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $recipient;

    /**
     * @ORM\ManyToOne(targetEntity=Messages::class, inversedBy="replies")
     */
    private ?Messages $parent;

    /**
     * @ORM\OneToMany(targetEntity=Messages::class, mappedBy="parent")
     */
    private Collection $replies;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="messages")
     */
    private Collection $file;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $type;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now');
        $this->replies = new ArrayCollection();
        $this->file = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParent($this);
        }

        return $this;
    }

    public function removeReply(self $reply): self
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParent() === $this) {
                $reply->setParent(null);
            }
        }

        return $this;
    }

    public function dashboardAdmin(): string
    {
        return "(5)";
    }

    public function getCreatedAtString(): string {
        $convert = new ConvertDate();
        $dateString = $convert->getStartDayFr($this->createdAt);
        $moisString = $convert->getStartMoisFr($this->createdAt);

        return $dateString. ' ' .$this->createdAt->format('d') . ' ' .$moisString . ' ' .$this->createdAt->format('Y') . ' à ' .
                $this->createdAt->format('H:i');

    }

    public function getTempsEcoule(): ?string {
        return $this->getTimeAgo(strtotime($this->getCreatedAt()->format("y-m-d H:i")));
    }

    public function getTimeAgo( $time )
    {
        $time_difference = time() - $time;

        if( $time_difference < 1 ) { return 'less than 1 second ago'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'ans',
            30 * 24 * 60 * 60       =>  'month',
            24 * 60 * 60            =>  'day',
            60 * 60                 =>  'hour',
            60                      =>  'minute',
            1                       =>  'second'
        );

        foreach( $condition as $secs => $str )
        {
            $d = $time_difference / $secs;

            if( $d >= 1 )
            {
                $t = round( $d );
                return $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }

    /**
     * @return Collection<int, File>
     */
    public function getFile(): Collection
    {
        return $this->file;
    }

    public function addFile(File $file): self
    {
        if (!$this->file->contains($file)) {
            $this->file[] = $file;
            $file->setMessages($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->file->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getMessages() === $this) {
                $file->setMessages(null);
            }
        }

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
