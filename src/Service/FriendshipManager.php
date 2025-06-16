<?php

namespace App\Service;

use App\Config\FriendshipStatus;
use App\Entity\FavoriteFriend;
use App\Entity\Friendship;
use App\Repository\FavoriteFriendRepository;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use  App\Entity\User;

class FriendshipManager implements FriendshipManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FriendshipRepository $repository,
        private readonly FavoriteFriendRepository $favoriteFriendRepository,
    ) {  
    }

    public function sendRequest(UserInterface $user, UserInterface $friend): void
    {
        if ($user === $friend) {
            throw new \InvalidArgumentException('You cant add Yourself to friends');
        }

        if ($this->repository->findFriendship($user, $friend)) {
            throw new \LogicException('You send request already');
        }

        $friendship = new Friendship();
        $friendship->setSender($user);
        $friendship->setReceiver($friend);

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

    public function removeRequest(Friendship $friendship, UserInterface $currentUser): void
    {
        if ($friendship->getSender() !== $currentUser AND $friendship->getReceiver() !== $currentUser) {
            throw new \LogicException('You are not allowed to modify this');
        }

        $this->entityManager->remove($friendship);
        $this->entityManager->flush();
    }

    public function favoritesToggle(Friendship $friendship, UserInterface $user): void
    {
        if ($friendship->getSender() !== $user AND $friendship->getReceiver() !== $user) {
            throw new \LogicException('You are not allowed to modify this');
        }

        $favoriteFriend = $this->favoriteFriendRepository->findOneBy(['owner' => $user, 'friendship' => $friendship]);

        if ($favoriteFriend) {
            $this->entityManager->remove($favoriteFriend);
        } else {
            $favoriteFriend = new FavoriteFriend();
            $favoriteFriend->setOwner($user);
            $favoriteFriend->setFriendship($friendship);
            $this->entityManager->persist($favoriteFriend);
        }

        $this->entityManager->flush();
    }
}