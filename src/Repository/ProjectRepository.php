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

    public function getProjectByUser($user)
    {
        $query = $this->createQueryBuilder('p');
        $query->select('p')
            ->InnerJoin('p.auteur', 'u', 'WITH', 'u = :user')
            ->setParameter('user', $user);

        return $query->getQuery()->getResult();
    }

    public function getProjectPublished($project = null): mixed
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.isPublished = 1')
        ;
        if($project){
            $ids = array();
            foreach ($project->getCategorie() as $categorie){
                $ids[] = $categorie->getId();
            }
            $query->leftJoin('p.categorie', 'cat');
            $query->andWhere($query->expr()->in('cat.id', $ids));
            $query->andWhere('p.id != :id')
                ->addOrderBy('p.id', 'DESC')
                ->setMaxResults(2)
                ->setParameter('id', $project->getId());
        }

        return $query->getQuery()->getResult();
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
            ->andWhere('p.isPublished = 1')
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
}
