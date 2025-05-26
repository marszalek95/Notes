<?php

namespace App\Service;

use App\Entity\Friendship;
use App\Entity\User;

interface FriendshipManagerInterface
{
    function sendRequest(User $sender, User $receiver): void;
    function acceptRequest(Friendship $friendship, User $currentUser): void;
    function rejectRequest(Friendship $friendship, User $currentUser): void;
}