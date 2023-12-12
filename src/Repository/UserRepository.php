<?php

namespace App\Repository;

use App\Entity\Candidature;
use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param int $longueur
     * @return string
     */
    public function genererMDP(int $longueur = 8): string
    {
        $mdp = "";

        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        $longueurMax = strlen($possible);

        if ($longueur > $longueurMax) {
            $longueur = $longueurMax;
        }

        $i = 0;

        while ($i < $longueur) {
            $caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);


            if (!strstr($mdp, $caractere)) {
                $mdp .= $caractere;
                $i++;
            }
        }
        return $mdp;
    }

    public function getInvitationByUser($user = null) {
        $query = $this->createQueryBuilder('e')
            ->andWhere('e.invite = :user')
            ->setParameter('user', $user);

        return $query->getQuery()->getResult();

    }

    public function getGroupMembers($user): mixed
    {
        $query = $this->createQueryBuilder('u');
        if ($user->isSuperAdmin()) {
            $query->select('u')
                ->andWhere('u.id != :id')
                ->setParameter('id', $user->getId());
        } elseif ($user->isAdmin()) {
            $query->select('u')
                ->andWhere('u.id != :id')
                ->setParameter('id', $user->getId());
        } elseif ($user->isMentor()) {
            $query->select('u')
                ->andWhere('u.id != :id')
                ->setParameter('id', $user->getId());
        }
        return $query->getQuery()->getResult();
    }

    public function getGroupMembersByUser(User $user): array
    {
        $userGroups = $user->getGroups();
        $usersInSameGroup = [];

        if ($userGroups->count() > 0) {
            $group = $userGroups->first();
            $members = $group->getMembers();

            foreach ($members as $member) {
                if ($member !== $user) {
                    $usersInSameGroup[] = $member;
                }
            }
        }

        return $usersInSameGroup;
    }
}
