<?php

namespace App\Repository;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use function Symfony\Component\String\u;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function findAllNotesQueryBuilder(UserInterface $user): QueryBuilder
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.sharedWith', 'sh')
            ->innerJoin('n.owner', 'owner')
            ->addSelect('owner')
            ->where('n.owner = :owner')
            ->orWhere('sh = :owner')
            ->setParameter('owner', $user)
            ->orderBy('n.createdAt', 'DESC');
    }

    public function findBySearchQuery(string $query, User $user): QueryBuilder
    {
        $searchTerms = $this->extractSearchTerms($query);
        if (0 === count($searchTerms)) {
            return $this->createQueryBuilder('n')
                ->where('1 = 0'); // return false 
        }

        $queryBuilder = $this->createQueryBuilder('n')
            ->andWhere('n.owner = :user')
            ->setParameter('user', $user);

        $orX = $queryBuilder->expr()->orX();

        foreach ($searchTerms as $key => $term) {
            $orX->add('n.title LIKE :t_'.$key);
            $queryBuilder->setParameter('t_'.$key, '%'.$term.'%');
        }

        $queryBuilder->andWhere($orX);

        $queryBuilder
            ->orderBy('n.createdAt', 'DESC')
        ;

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
