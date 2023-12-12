<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Categorie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categorie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categorie[]    findAll()
 * @method Categorie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    /**
     * @return array
     */
    public function findLatest(): array {
        $query = $this->createQueryBuilder('a');
        $query->setMaxResults(6);
        return $query->getQuery()->getResult();
    }


    /**
     * @return array
     */
    public function getCategoriesWithProjects(): array
    {
        $ids = array();
        $query = $this->getEntityManager()->getRepository(Project::class)->createQueryBuilder('p')
            ->orderBy('p.nom', 'ASC')
        ;
        $result = $query->getQuery()->getResult();
        foreach($result as $project){
            $ids[] = $project->getCategorie();
        }
        return $ids;
    }
}
