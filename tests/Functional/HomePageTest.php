<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->selectLink('Inscription'));
        $this->assertCount(1, $crawler->selectLink('Connexion'));
        $this->assertCount(1, $crawler->filter('.last-add'));

        $this->assertSelectorTextContains('h1', 'Annonces');
    }
}
