<?php

namespace App\DataFixtures;

use App\Config\FriendshipStatus;
use App\Factory\FriendshipFactory;
use App\Factory\NoteFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FriendshipFixtures extends Fixture implements  FixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $user = UserFactory::createOne(['email' => 'test@example.com'])->_real();
        FriendshipFactory::createMany(30, ['sender' => $user, 'status' => FriendshipStatus::Accepted]);
        NoteFactory::createMany(120, ['owner' => $user]);
    }
}
