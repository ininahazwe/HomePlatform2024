<?php

namespace App\Data;

use App\Entity\Categorie;

class SearchDataProject
{
    /**
     * @var int
     */
    public int $page = 1;

    /**
     * @var string
     */
    public string $q;

    /**
     * @var Categorie[]
     */
    public array $categories = []   ;
}
