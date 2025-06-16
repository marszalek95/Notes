<?php

namespace App\Controller;

use App\Repository\FriendshipRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FriendsController extends AbstractController
{
    #[Route('/friends', name: 'app_friends', methods: ['GET'])]
    public function index(FriendshipRepository $friendshipRepository, Request $request): Response
    {
        $user = $this->getUser();
        $queryBuilder = $friendshipRepository->findAllAcceptedFriendships($user);
        $page = $request->query->get('page', 1);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(12);
        $pagerfanta->setCurrentPage($page);

        return $this->render('friends/index.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/friends/requests', name: 'app_friends_pending', methods: ['GET'])]
    public function pending(FriendshipRepository $friendshipRepository, Request $request): Response
    {
        $user = $this->getUser();
        $queryBuilder = $friendshipRepository->findAllPendingFriendships($user);
        $page = $request->query->get('page', 1);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(12);
        $pagerfanta->setCurrentPage($page);

        return $this->render('friends/pending.html.twig', [
            'pager' => $pagerfanta,
        ]);
    }

    #[Route('/friends/search', name: 'app_friends_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->render('users/search.html.twig', [
            'query' => (string) $request->query->get('q', ''),
            'page' => (int) $request->query->get('page', 1),
        ]);
    }
}
