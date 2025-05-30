<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use function Symfony\Component\String\u;

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

    public function findBySearchQuery(string $query, User $user): QueryBuilder
    {
        $searchTerms = $this->extractSearchTerms($query);
        if (0 === count($searchTerms)) {
            return $this->createQueryBuilder('u')
                ->where('1 = 0'); // return false
        }

        $queryBuilder = $this->createQueryBuilder('u');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('u.email LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%');
        }

        return $queryBuilder;
    }

    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim()->split(' '));

        return array_filter($terms, static function ($term) {
            return 2 <= $term->length();
        });
    }
}
