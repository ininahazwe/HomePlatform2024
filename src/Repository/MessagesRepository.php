<?php

namespace App\Repository;

use App\Entity\Messages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    public function getReceived($user)
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
    public function getReceivedUsers($user): ArrayCollection
    {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->andWhere('m.recipient = :user OR m.sender = :user')
            ->setParameter('user', $user)
            ->orderBy('m.id', 'DESC')
        ;
        $result = $query->getQuery()->getResult();
        $users = new ArrayCollection();
        foreach ($result as $message){
            if ($message->getSender() == $user){
                if (!$users->contains($message->getRecipient())) {
                    $users->add($message->getRecipient());
                }
            }
            if ($message->getRecipient() == $user){
                if (!$users->contains($message->getSender())) {
                    $users->add($message->getSender());
                }
            }
        }
        return $users;
    }



    public function countMsgNotRead($user, $sender = null): int|string {
        $nb = "0";

        $ids = array();
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.is_read = 0')
            ->andWhere('m.recipient = :user')
            ->setParameter('user', $user)
        ;
        if($sender){
            $query
                ->andWhere('m.sender = :sender')
                ->setParameter('sender', $sender)
            ;
        }
        $result = $query->getQuery()->getResult();
        foreach($result as $message){
            $ids[] = $message->getId();
        }
        if (count($ids) > 0){
            return count($ids);
        }
        return $nb;
    }


    public function getAllMessages($moi, $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->andWhere('m.recipient in (:users)')
            ->andWhere('m.sender in (:users)')
            ->setParameter('users', [$user, $moi])
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function getAllMessagesReceivedNotRead($moi, $user)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->andWhere('m.is_read = 0')
            ->andWhere('m.recipient = :recipient')
            ->andWhere('m.sender = :sender')
            ->setParameter('recipient', $moi)
            ->setParameter('sender', $user)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getLastMessage($moi, $user): string {
        $query = $this->createQueryBuilder('m')
            ->andWhere('m.parent IS NULL')
            ->andWhere('m.recipient = :recipient')
            ->andWhere('m.sender = :sender')
            ->setParameter('recipient', $moi)
            ->setParameter('sender', $user)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
        ;
        $result = $query->getQuery()->getOneOrNullResult();
        if ($result){
            return $result->getTempsEcoule();
        }
        return '';
    }
}
