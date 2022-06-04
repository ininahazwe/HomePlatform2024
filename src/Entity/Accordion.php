<?php

namespace App\Entity;

use App\Repository\AccordionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccordionRepository::class)
 */
class Accordion
{
    use ResourceId;
    use Timestapable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $titre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @return string
     */
    public function __toString(): string {
        return $this->titre;
    }


    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): self
    {
        $this->titre = $titre;

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
}
