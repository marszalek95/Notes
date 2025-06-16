<?php

namespace App\Tests\Controller;

use App\Config\FriendshipStatus;
use App\Factory\FriendshipFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class FriendsControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;
    public function testIndex(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();
        FriendshipFactory::createMany(5, ['sender' => $user, 'status' => FriendshipStatus::Accepted]);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/friends');
        $this->assertResponseIsSuccessful();
        $this->assertCount(5, $crawler->filter('.friend'));
    }
}
