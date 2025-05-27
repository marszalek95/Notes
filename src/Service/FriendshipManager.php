<?php

namespace App\Service;

use App\Config\FriendshipStatus;
use App\Entity\Friendship;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendshipManager implements FriendshipManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FriendshipRepository $repository,
    ) {  
    }

    public function sendRequest(UserInterface $sender, UserInterface $receiver): void
    {
        if ($sender === $receiver) {
            throw new \InvalidArgumentException('You cant add Yourself to friends');
        }

        if ($this->repository->findFriendship($sender, $receiver)) {
            throw new \LogicException('You send request already');
        }

        $friendship = new Friendship();
        $friendship->setSender($sender);
        $friendship->setReceiver($receiver);

        $this->entityManager->persist($friendship);
        $this->entityManager->flush();
    }

    public function acceptRequest(Friendship $friendship, UserInterface $currentUser): void
    {
        if ($friendship->getReceiver() !== $currentUser) {
            throw new \LogicException('You are not receiver');
        }

        $friendship->setStatus(FriendshipStatus::Accepted);
        $this->entityManager->flush();
    }

    public function rejectRequest(Friendship $friendship, UserInterface $currentUser): void
    {
        if ($friendship->getReceiver() !== $currentUser) {
            throw new \LogicException('You are not receiver');
        }

        $friendship->setStatus(FriendshipStatus::Rejected);
        $this->entityManager->flush();
    }
}