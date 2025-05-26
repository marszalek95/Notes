<?php

namespace App\Tests\Controller;

use App\Config\FriendshipStatus;
use App\Factory\FriendshipFactory;
use App\Factory\UserFactory;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FriendshipControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    function testSendRequest(): void
    {
        $client = static::createClient();

        $sender = UserFactory::createOne()->_real();
        $receiver = UserFactory::createOne()->_real();

        $client->loginUser($sender);
        $client->request('POST', '/friends/add/' . $receiver->getId());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($sender, $receiver);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Pending);
        
    }

    function testAcceptRequest(): void
    {
        $client = static::createClient();

        $friendship = FriendshipFactory::createOne();
        $sender = $friendship->getSender();
        $receiver = $friendship->getReceiver();

        $client->loginUser($receiver);
        $client->request('POST', '/friends/accept/' . $friendship->getId());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($sender, $receiver);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Accepted);
        
    }

    function testRejectRequest(): void
    {
        $client = static::createClient();

        $friendship = FriendshipFactory::createOne();
        $sender = $friendship->getSender();
        $receiver = $friendship->getReceiver();

        $client->loginUser($receiver);
        $client->request('POST', '/friends/reject/' . $friendship->getId());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($sender, $receiver);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Rejected);
        
    }

    
}