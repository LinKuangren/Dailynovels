<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    public function testIfLoginSuccessful(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");
        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "dailynovels@gmail.com",
            "_password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        //$this->assertRouteSame('accueil');
    }

    public function testIfLoginFailedPassword(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "dailynovels@gmail.com",
            "_password" => "1234567frdh"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('security_login');

        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials.");
    }

}
