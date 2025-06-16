<?php

namespace App\Repository;

use App\Config\FriendshipStatus;
use App\Entity\FavoriteFriend;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Entity\Friendship;
/**
 * @extends ServiceEntityRepository<User>
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
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findBySearchQuery(string $query, $currentUser): QueryBuilder
    {
        if (!$query) {
            return $this->createQueryBuilder('u')
                ->where('1 = 0'); // return false
        }

        return $this->createQueryBuilder('u')
            ->select('partial u.{id, email} AS user')
            ->leftJoin(
                Friendship::class,
                'f',
                'WITH',
                '(f.sender = :currentUser AND f.receiver = u) OR (f.sender = u AND f.receiver = :currentUser)'
            )
            ->leftJoin(
                FavoriteFriend::class,
                'favorite',
                'WITH',
                'favorite.owner = :currentUser AND favorite.friendship = f.id'
            )
            ->addSelect('f.status AS friendshipStatus')
            ->addSelect('f.id AS friendshipId')
            ->addSelect('CASE WHEN favorite.id IS NOT NULL THEN true ELSE false END AS isFavorite')
            ->addSelect('CASE WHEN f.receiver = :currentUser THEN true ELSE false END AS isReceiver')
            ->where('u.email LIKE :email')
            ->andWhere('u != :currentUser')
            ->setParameter('currentUser', $currentUser)
            ->setParameter('email', '%'.$query.'%');
    }
}
