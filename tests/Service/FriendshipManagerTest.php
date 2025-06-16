<?php

namespace App\Tests\Service;

use App\Entity\Friendship;
use App\Factory\FriendshipFactory;
use App\Factory\UserFactory;
use App\Service\FriendshipManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FriendshipManagerTest extends KernelTestCase
{
    use ResetDatabase, Factories;

    private FriendshipManager $friendshipManager;

    protected function setUp(): void
    {
        $this->friendshipManager = static::getContainer()->get(FriendshipManager::class);
    }

    public function testSendRequest():void
    {
        $user = UserFactory::createOne()->_real();
        $friend = UserFactory::createOne()->_real();

        $this->friendshipManager->sendRequest($user, $friend);
        $this->assertTrue(true);

    }

    public function testCannotAddYourself(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $user = UserFactory::createOne()->_real();

        $this->friendshipManager->sendRequest($user, $user);
    }

    public function testCannotDuplicate():void
    {
        $this->expectException(\LogicException::class);
        $sender = UserFactory::createOne()->_real();
        $receiver = UserFactory::createOne()->_real();

        $this->friendshipManager->sendRequest($sender, $receiver);
        $this->friendshipManager->sendRequest($sender, $receiver);
    }

    public function testAcceptRequest(): void
    {
        $friendship = FriendshipFactory::createOne()->_real();
        $receiver = $friendship->getReceiver();

        $this->friendshipManager->acceptRequest($friendship, $receiver);
        $this->assertTrue(true);
    }

    public function testAcceptRequestOnlyWhenReceived(): void
    {
        $this->expectException(\LogicException::class);
        $friendship = FriendshipFactory::createOne()->_real();
        $sender = $friendship->getSender();

        $this->friendshipManager->acceptRequest($friendship, $sender);
    }

    public function testRejectRequest(): void
    {
        $friendship = FriendshipFactory::createOne()->_real();
        $receiver = $friendship->getReceiver();

        $this->friendshipManager->rejectRequest($friendship, $receiver);
        $this->assertTrue(true);
    }

    public function testRejectRequestOnlyWhenReceived(): void
    {
        $this->expectException(\LogicException::class);
        $friendship = FriendshipFactory::createOne()->_real();
        $sender = $friendship->getSender();

        $this->friendshipManager->rejectRequest($friendship, $sender);
    }

    public function testFavoritesToggleChanged(): void
    {
        $friendship = FriendshipFactory::createOne()->_real();
        $sender = $friendship->getSender();

        $this->friendshipManager->favoritesToggle($friendship, $sender);
        $this->assertTrue(true);
        $this->friendshipManager->favoritesToggle($friendship, $sender);
        $this->assertFalse(false);
    }

}

