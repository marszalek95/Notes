<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Service\FriendshipManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    public function __construct(
        private readonly FriendshipManagerInterface $friendshipManager,
    ) {
    }

    #[Route('/friends/add/{id}', name: 'app_friends_add', methods: ['POST'])]
    public function sendRequest(User $receiver, Request $request): RedirectResponse
    {
        $sender = $this->getUser();
        $route = $request->headers->get('referer');

        $this->checkCsrfToken('send-request', $request);

        try {
            $this->friendshipManager->sendRequest($sender, $receiver);
            $this->addFlash('success', 'Invitation send');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($route);
    }

    #[Route('/friends/accept/{id}', name: 'app_friends_accept', methods: ['POST'])]
    public function acceptRequest(Friendship $friendship, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        $route = $request->headers->get('referer');

        $this->checkCsrfToken('accept-request', $request);

        try {
            $this->friendshipManager->acceptRequest($friendship, $user);
            $this->addFlash('success', 'Accepted invitation');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($route);
    }

    #[Route('friends/reject/{id}', name: 'app_friends_reject', methods: ['POST'])]
    public function rejectRequest(Friendship $friendship, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        $route = $request->headers->get('referer');

        $this->checkCsrfToken('reject-request', $request);

        try {
            $this->friendshipManager->rejectRequest($friendship, $user);
            $this->addFlash('success', 'Rejected invitation');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($route);
    }

    #[Route('friends/remove/{id}', name: 'app_friends_remove', methods: ['POST'])]
    public function removeRequest(Friendship $friendship, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        $route = $request->headers->get('referer');

        $this->checkCsrfToken('remove-request', $request);

        try {
            $this->friendshipManager->removeRequest($friendship, $user);
            $this->addFlash('success', 'Friend removed');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($route);
    }

    #[Route('/friends/favorites/{id}', name: 'app_friends_favorites', methods: ['POST'])]
    public function favoritesToggle(Friendship $friendship, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        $route = $request->headers->get('referer');

        $this->checkCsrfToken('favorites-toggle', $request);

        try {
            $this->friendshipManager->favoritesToggle($friendship, $user);;
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($route);
    }

    protected  function checkCsrfToken(string $id, Request $request): void
    {
        $token = $request->getPayload()->get('token');

        if (!$this->isCsrfTokenValid($id, $token)) {
            throw new AccessDeniedException('Invalid CSRF token');
        }
    }
}