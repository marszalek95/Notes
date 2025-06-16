<?php

namespace App\Tests\Controller;

use App\Config\FriendshipStatus;
use App\Factory\FriendshipFactory;
use App\Factory\UserFactory;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FriendshipControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    function testSendRequest(): void
    {
        $sender = UserFactory::createOne()->_real();
        $receiver = UserFactory::createOne()->_real();

        $this->client->loginUser($sender);
        $crawler = $this->client->request('GET', '/friends/search?query=' . $receiver->getEmail());
        $this->client->submit($crawler->filter('#send-request')->form());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($sender, $receiver);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Pending);
        
    }

    function testAcceptRequest(): void
    {
        $friendship = FriendshipFactory::createOne();
        $user = $friendship->getSender();
        $friend = $friendship->getReceiver();

        $this->client->loginUser($friend);
        $crawler = $this->client->request('GET', '/friends/requests');
        $this->client->submit($crawler->filter('#accept-request')->form());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($user, $friend);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Accepted);

    }

    function testRejectRequest(): void
    {
        $friendship = FriendshipFactory::createOne();
        $user = $friendship->getSender();
        $friend = $friendship->getReceiver();

        $this->client->loginUser($friend);
        $crawler = $this->client->request('GET', '/friends/requests');
        $this->client->submit($crawler->filter('#reject-request')->form());
        $this->assertResponseRedirects();

        $repository = static::getContainer()->get(FriendshipRepository::class);
        $friendship =  $repository->findFriendship($user, $friend);
        $this->assertNotNull($friendship);
        $this->assertSame($friendship->getStatus(), FriendshipStatus::Rejected);

    }

    
}