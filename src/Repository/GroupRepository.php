<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findUsersInSameGroup(User $user)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.members', 'm') // Assurez-vous que 'members' est le champ qui représente les utilisateurs dans un groupe
            ->where(':user MEMBER OF g.members')
            ->andWhere('m != :user') // Exclut l'utilisateur en cours de la liste
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
