<?php

namespace App\Config;

enum FriendshipStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
}