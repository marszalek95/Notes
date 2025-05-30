<?php

namespace App\Controller;

use App\Repository\FriendshipRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FriendsController extends AbstractController
{
    #[Route('/friends', name: 'app_friends')]
    public function index(FriendshipRepository $friendshipRepository, \Symfony\Component\HttpFoundation\Request $request): Response
    {
        $user = $this->getUser();
        $queryBuilder = $friendshipRepository->findAllAcceptedFriendships($user);

        $pagerfanta = new Pagerfanta(new QueryAdapter($queryBuilder));
        $pagerfanta->setMaxPerPage(12);
        $pagerfanta->setCurrentPage($request->query->get('page', 1));

        return $this->render('friends/index.html.twig', [
            'friendships' => $pagerfanta,
        ]);
    }

    #[Route('/friends/search', name: 'app_friends_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        return $this->render('friends/search.html.twig', [
            'query' => (string) $request->query->get('q', ''),
            'page' => (int) $request->query->get('page', 1),
        ]);
    }
}
