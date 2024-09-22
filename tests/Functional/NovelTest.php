<?php

namespace App\Tests\Functional;

use App\Entity\Novels;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NovelPantherTest extends WebTestCase
{
    public function testCreateNovelSuccessful(): void
    {
        $client = static::createClient();
        
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $crawler = $client->request('GET', $urlGenerator->generate('novels_ajout'));

        $form = $crawler->filter('form[name=novels]')->form([
            'novels[title]' => 'New Novel',
            'novels[description]' => 'This is a description of the new novel.',
            'novels[author]' => 'Author Name',
            'novels[statut]' => 'en cours',
            'novels[visibilitie]' => 1,
        ]);

        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La novel est créé.');

        $this->assertRouteSame('novels');
    }

    public function testUpdateNovelSuccessful(): void
    {
        $client = static::createClient();
        
        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $novel = $entityManager->find(Novels::class, 5);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('novels_modifier', [
                'id' => $novel->getId(),
                'title' => $novel->getTitle()
            ])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=novels]')->form([
            'novels[title]' => 'Updated Novel Title',
            'novels[description]' => 'This is an updated description of the novel.',
        ]);

        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La novel est modifié.');

        $this->assertRouteSame('novels');
    }

    public function testDeleteNovelSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $novel = $entityManager->find(Novels::class, 5);

        $client->loginUser($user);

        $client->request('GET', $urlGenerator->generate('novels_delete', ['id' => $novel->getId()]));

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La novel est supprimé.');

        $this->assertRouteSame('novels');
    }
}
