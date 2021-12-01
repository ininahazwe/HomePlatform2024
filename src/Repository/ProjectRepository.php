<?php

namespace App\Repository;

use App\Data\SearchDataProject;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    private PaginatorInterface $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator) {
        parent::__construct($registry, Project::class);
        $this->paginator = $paginator;
    }

    /**
     * @return Query
     */
    public function getProjectsRandomly(): Query
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.nom')
            ->orderBy('RAND()')
            ->setMaxResults(2)
        ;
        return $query->getQuery();
    }

    /**
     * @param SearchDataProject $search
     * @return PaginationInterface
     */
    public function findSearch(SearchDataProject $search): PaginationInterface {
        $query = $this->getSearchQuery($search)->getQuery();
        return $this->paginator->paginate(
            $query,
            $search->page,
            10
        );
    }

    public function getSearchQuery(SearchDataProject $search): QueryBuilder {
        $query = $this
            ->createQueryBuilder('p')
        ;


        if(!empty($search->q)){
            $query
                ->andWhere('p.nom LIKE :q')
                ->setParameter('q', "%" . $search->q . "%");
        }

        if(!empty($search->categories)){
            $query = $query
                ->innerJoin('p.categorie', 'c')
                ->andWhere('c.id IN (:categorie)')
                ->setParameter('categorie', $search->categories);
        }

        return $query;
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
