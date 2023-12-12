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
        if($user->isCandidat()) {
            $query = $this->createQueryBuilder('p');
            $query->select('p')
                ->InnerJoin('p.auteur', 'u', 'WITH', 'u = :user')
                ->orderBy('p.id', 'DESC')
                ->setParameter('user', $user);
        }else{
            $query = $this->createQueryBuilder('p')
                ->select('p')
                ->orderBy('p.id', 'DESC');
        }
        return $query->getQuery()->getResult();
    }

    public function getProjectPublished($project = null): mixed
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.statut = 1')
            ->orderBy('p.id', 'DESC')
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
            ->andWhere('p.statut = 1')
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

    /**
     * Recherche les projects en fonction du formulaire
     */
    public function search($mots = null, $categorie = null){
        $query = $this->createQueryBuilder('p');
        $query->where('p.statut = 1');
        if($mots != null){
            $query->andWhere('MATCH_AGAINST(p.nom, a.description) AGAINST (:mots boolean)>0')
                ->setParameter('mots', $mots);
        }
        if($categorie != null){
            $query->leftJoin('p.categorie', 'c');
            $query->andWhere('c.id = :id')
                ->setParameter('id', $categorie);
        }
        return $query->getQuery()->getResult();
    }

    public function getProjetsSimilaire($project)
    {
        $ids = [];
        if(count($project->getCategorie()) > 0) {
            foreach($project->getCategorie() as $cat){
                $ids[] = $cat->getId();
            }
        } else {
            return null;
        }

        $query = $this->createQueryBuilder('p');
        $query->leftJoin('p.categorie', 'cat')
            ->andWhere($query->expr()->in('cat.id', $ids))
            ->andWhere('p.id != :id')
            ->setParameter('id', $project->getId());

        return $query->getQuery()->getResult();
    }

    public function findCountSearch(SearchDataProject $search): int {
        $query = $this->getSearchQuery($search)->getQuery();
        return count($query->getScalarResult());
    }

    public function getProjectsBySdg($categorie)
    {
        $query = $this->createQueryBuilder('p')
                ->addOrderBy('p.id', 'DESC');
        $ids = array();
        $ids[] = $categorie->getId();
             $query->leftJoin('p.categorie', 'categorie');
             $query->andWhere($query->expr()->in('categorie.id', $ids));
        return $query->getQuery()->getResult();
    }
}
