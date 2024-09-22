<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormContactTest extends WebTestCase
{
    public function testIsContactSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Contact');

        $submitButton = $crawler->selectButton('Envoyer');
        $form = $submitButton->form();

        $form["contact[email]"] = "romaindanne@gmail.com";
        $form["contact[subject]"] = "Ceci est un sujet";
        $form["contact[message]"] = "Voici mon texte de mon email.";
        
        $client->submit($form);

        $this->assertTrue($client->getResponse()->isSuccessful());

        // $this->assertSelectorTextContains(
        //     'div.alert-success',
        //     'Votre demande à été envoyé avec succès !'
        // );
    }
}
