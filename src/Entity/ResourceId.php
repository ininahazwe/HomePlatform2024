<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait ResourceId
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"read:project", "read:team", "read:edition", "read:about", "read:partenaire", "read:cat"})
     */
    private ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
