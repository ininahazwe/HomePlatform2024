<?php

namespace App\Repository;

use App\Entity\Dictionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Dictionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dictionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dictionnaire[]    findAll()
 * @method Dictionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DictionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dictionnaire::class);
    }

    // /**
    //  * @return Dictionnaire[] Returns an array of Dictionnaire objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dictionnaire
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
