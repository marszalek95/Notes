<?php

namespace App\Controller;

use App\Entity\Friendship;
use App\Entity\User;
use App\Service\FriendshipManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    public function __construct(
        private readonly FriendshipManagerInterface $friendshipManager,
    ) {
    }

    #[Route('/friends/add/{id}', name: 'app_friends_add', methods: ['POST'])]
    public function sendRequest(User $receiver): RedirectResponse
    {
        $sender = $this->getUser();

        try {
            $this->friendshipManager->sendRequest($sender, $receiver);
            $this->addFlash('success', 'Invitation send');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_notes');
    }

    #[Route('/friends/accept/{id}', name: 'app_friends_accept', methods: ['POST'])]
    public function acceptRequest(Friendship $friendship): RedirectResponse
    {
        $user = $this->getUser();

        try {
            $this->friendshipManager->acceptRequest($friendship, $user);
            $this->addFlash('success', 'Accepted invitation');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_notes');
    }

    #[Route('friends/reject/{id}', name: 'app_friends_reject', methods: ['POST'])]
    public function rejectRequest(Friendship $friendship): RedirectResponse
    {
        $user = $this->getUser();

        try {
            $this->friendshipManager->rejectRequest($friendship, $user);
            $this->addFlash('success', 'Rejected invitation');
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('app_notes');
    }
}