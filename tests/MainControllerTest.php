<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Serie's detail");
    }

    // test redirection si non logger
    public function testCreateSerieIsWorkingIfNotLogged(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/serie/add');

        $this->assertResponseRedirects('/login', 302);

    }

    // test si loggé donc simuler une connexion
    public function testCreateSerieIsWorkingIfLogged(): void
    {
        $client = static::createClient();

        // récupéré un user dans la BDD
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => "sandra.lamire@live.fr"]);

        // tester la connexion
        $client->loginUser($user);

        $crawler = $client->request('GET', '/serie/add');
        $this->assertResponseIsSuccessful();
    }
}
