<?php

namespace App\Twig\Components;

use App\Repository\UserRepository;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FriendSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, onUpdated: 'onQueryUpdated', url: true)]
    public string $query = '';

    #[LiveProp(writable: true, url: true)]
    public int $page = 1;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly Security $security,
        private readonly RouterInterface $router,
    ) {
    }

    public function onQueryUpdated(): void
    {
        $this->page = 1;
    }

    public function getUsers(): Pagerfanta
    {
        $user = $this->security->getUser();

        $queryBuilder = $this->userRepository->findBySearchQuery($this->query, $user);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(12);
        $pagerfanta->setCurrentPage($this->page);
        $pagerfanta->getCurrentPageResults();

        return $pagerfanta;
    }
}