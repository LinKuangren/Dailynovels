<?php

namespace App\Tests\Functional;

use App\Entity\Categories;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class CategorieTest extends WebTestCase
{
    public function testCreateCategorieSuccessfull(): void
    {
        $client = static::createClient();
        
        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('categories_ajout'));

        $form = $crawler->filter('form[name=categories]')->form([
            'categories[name]' => "categorie",
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La catégorie est créé.');

        $this->assertRouteSame('categories');
    }

    public function testUpdateCategorieSuccessfull(): void
    {
        $client = static::createClient();
        
        $urlGenerator = $client->getContainer()->get("router");

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $categorie = $entityManager->find(Categories::class, 30);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('categories_modifier', ['id' => $categorie->getId()])
        );

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=categories]')->form([
            'categories[name]' => "categorie 2",
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La categorie est modifié.');

        $this->assertRouteSame('categories');
    }

    public function testDeleteCategorieSuccessful(): void
    {
        $client = static::createClient();

        $urlGenerator = $client->getContainer()->get('router');
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $user = $entityManager->find(User::class, 1);
        $categorie = $entityManager->find(Categories::class, 30);

        $client->loginUser($user);

        $crawler = $client->request(
            Request::METHOD_GET,
            $urlGenerator->generate('categories_delete', ['id' => $categorie->getId()])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('div.alert-success', 'La categorie est supprimé.');

        $this->assertRouteSame('categories');
    }

}
