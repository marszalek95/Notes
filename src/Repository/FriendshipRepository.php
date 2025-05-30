<?php

namespace App\Repository;

use App\Config\FriendshipStatus;
use App\Entity\Friendship;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function findFriendship(UserInterface $user1, UserInterface $user2): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->where('(f.sender = :user1 AND f.receiver = :user2) OR (f.sender = :user2 AND f.receiver = :user1)')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllAcceptedFriendships(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->addSelect('sender', 'receiver')
            ->innerJoin('f.sender', 'sender')
            ->innerJoin('f.receiver', 'receiver')
            ->where('f.status = :status')
            ->andWhere('sender.id = :user OR receiver.id = :user')
            ->setParameter('status', FriendshipStatus::Accepted)
            ->setParameter('user', $user);

    }
}
