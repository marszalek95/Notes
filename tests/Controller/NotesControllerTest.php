<?php

namespace App\Tests\Controller;

use App\Factory\NoteFactory;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class NotesControllerTest extends WebTestCase
{
    use ResetDatabase, Factories;
    
    public function testShowNotes(): void
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();
        NoteFactory::createMany(48, ['owner' => $user]);

        $client->loginUser($user);
        $client->request('GET', '/notes');
        $this->assertResponseIsSuccessful();
        $client->request('GET', '/notes?page=8');
        $this->assertResponseIsSuccessful();
    }
    
    public function testCreateNote() : void 
    {
        $client = static::createClient();

        $user = UserFactory::createOne()->_real();
        $client->loginUser($user);

        $client->request('GET', '/notes/add');
        // $buttonCrawlerNode = $crawler->selectButton('submit');
        // $form = $buttonCrawlerNode->form();

        $client->submitForm('Submit', [
            'note_form[title]' => 'Test Title',
            'note_form[content]' => 'Test content',
        ]);
        $this->assertResponseRedirects();
    }
}