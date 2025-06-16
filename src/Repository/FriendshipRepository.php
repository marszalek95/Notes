<?php

namespace App\Repository;

use App\Entity\FavoriteFriend;
use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Config\FriendshipStatus;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function findFriendship(UserInterface $user, UserInterface $friend): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->where('(f.sender = :user AND f.receiver = :friend) OR (f.receiver = :user AND f.sender = :friend)')
            ->setParameter('user', $user)
            ->setParameter('friend', $friend)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllAcceptedFriendships(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->select('f AS friendship')
            ->leftJoin('f.sender', 'sender')
            ->leftJoin('f.receiver', 'receiver')
            ->leftJoin(
                FavoriteFriend::class,
                'favorite',
                'WITH',
                'favorite.owner = :currentUser AND favorite.friendship = f.id'
            )
            ->addSelect('sender', 'receiver')
            ->addSelect('CASE WHEN favorite.id IS NOT NULL THEN true ELSE false END AS isFavorite')            ->where('u.email LIKE :email')
            ->where('f.status = :status')
            ->andWhere('f.sender = :currentUser OR f.receiver = :currentUser')
            ->setParameter('status', FriendshipStatus::Accepted)
            ->setParameter('currentUser', $user);
    }

    public function findAllPendingFriendships(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->where('f.receiver = :user')
            ->andWhere('f.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', FriendshipStatus::Pending);
    }
}
