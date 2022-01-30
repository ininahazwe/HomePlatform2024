<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Messages|null find($id, $lockMode = null, $lockVersion = null)
 * @method Messages|null findOneBy(array $criteria, array $orderBy = null)
 * @method Messages[]    findAll()
 * @method Messages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Messages::class);
    }

    public function getResponses($message)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent = :parent')
            ->setParameter('parent', $message)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getReceived($user): mixed
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->andWhere('m.recipient = :user OR m.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;

    }

    /**
     * @param $user
     * @return int|string
     */
    public function countMsgNotRead($user): int|string
    {
        $nb = "0";

        $ids = array();
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.is_read = 0')
            ->andWhere('m.recipient = :user')
            ->setParameter('user', $user)
        ;
        $result = $query->getQuery()->getResult();
        foreach($result as $message){
            $ids[] = $message->getId();
        }
        if (count($ids) > 0){
            return count($ids);
        }
        return $nb;

    }
}
