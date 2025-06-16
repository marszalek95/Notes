<?php

namespace App\Service;

use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FavoriteFriendRepository;
use Symfony\Component\Security\Core\User\UserInterface;

interface FriendshipManagerInterface
{
    public function sendRequest(UserInterface $sender, UserInterface $receiver): void;
    public function acceptRequest(Friendship $friendship, UserInterface $currentUser): void;
    public function rejectRequest(Friendship $friendship, UserInterface $currentUser): void;
    public function removeRequest(Friendship $friendship, UserInterface $currentUser): void;
    public function favoritesToggle(Friendship $friendship, UserInterface $currentUser): void;
}