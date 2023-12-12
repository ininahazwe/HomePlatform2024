<?php

namespace App\Entity;

use App\Repository\DesinscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DesinscriptionRepository::class)
 */
class Desinscription
{
    const GENRE_HOMME = 'Man';
    const GENRE_FEMME = 'Woman';
    const GENRE_AUTRE = 'Other';

    const MOTIF_1 = 1;
    const MOTIF_2 = 2;
    const MOTIF_3 = 3;
    const MOTIF_4 = 4;
    const MOTIF_5 = 5;

    use ResourceId;
    use Timestapable;

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
    private ?string $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?int $genre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $motif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $autre_motif;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $dateInscription;

    public function __construct()
    {
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getMotif(): ?int
    {
        return $this->motif;
    }

    public function setMotif(?int $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getAutreMotif(): ?string
    {
        return $this->autre_motif;
    }

    public function setAutreMotif(?string $autre_motif): self
    {
        $this->autre_motif = $autre_motif;

        return $this;
    }
    public static function getGenreList(): array {
        return [
            'Homme' => self::GENRE_HOMME,
            'Femme' => self::GENRE_FEMME,
            'Non précisé' => self::GENRE_AUTRE,
        ];
    }
    public function getGenreName():string
    {
        return match ($this->genre) {
            self::GENRE_HOMME => 'Homme',
            self::GENRE_FEMME => 'Femme',
            self::GENRE_AUTRE => 'Non précisé',
            default => 'null',
        };
    }


    public static function getMotifList(): array {
        return [
            "J'ai trouvé un emploi via Talents Handicap" => self::MOTIF_1,
            "J'ai trouvé un emploi via un autre biais que Talents Handicap" => self::MOTIF_2,
            "Je ne suis plus en recherche d'emploi" => self::MOTIF_3,
            "Je ne suis pas satisfait de vos services" => self::MOTIF_4,
            "Autre" => self::MOTIF_5,
        ];
    }
    public function getMotifName():string
    {
        return match ($this->motif) {
            self::MOTIF_1 => "J'ai trouvé un emploi via Talents Handicap",
            self::MOTIF_2 => "J'ai trouvé un emploi via un autre biais que Talents Handicap",
            self::MOTIF_3 => "Je ne suis plus en recherche d'emploi",
            self::MOTIF_4 => "Je ne suis pas satisfait de vos services",
            self::MOTIF_5 => "Autre",
            default => 'null',
        };
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }
}
