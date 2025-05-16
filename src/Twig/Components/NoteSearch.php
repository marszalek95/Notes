<?php

namespace App\Twig\Components;

use App\Repository\NoteRepository;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class NoteSearch
{
    use DefaultActionTrait;

    #[LiveProp(writable: true, url: true)]
    public string $query = '';

    #[LiveProp(writable: true, url: true)]
    public string $page;

    public function __construct(
        private readonly NoteRepository $noteRepository,
        private readonly Security $security,
        private readonly RouterInterface $router,
    ) {  
    }

    public function getNotes(): Pagerfanta
    {
        $user = $this->security->getUser();

        $queryBuilder = $this->noteRepository->findBySearchQuery($this->query, $user);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(3);
        $pagerfanta->setCurrentPage($this->page);

        return $pagerfanta;
    }
}