<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends WebTestCase
{
    //Vérifie que l'url non-API fonctionne et renvoi du HTML
    public function testGetAllBooksHtml(): void
    {
        $client = $this->callClient('GET','/books');

        //Vérifie que la requête Http renvoi quelque chose
        $this->assertResponseIsSuccessful();

        //Récupère la réponse et vérifie son format
        $type = $client->getResponse()->headers->get('content-type');
        $this->assertEquals('text/html; charset=UTF-8', $type);
    }

    //Vérifie que l'url API fonctionne et renvoi du Json
    public function testGetAllBooksJson(): void
    {
        $client = static::createClient();
        //Récupère un utilisateur dans la bdd
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('user@fakebookemail.com');
        //Connecte l'utilisateur
        $client->loginUser($user);

        //Envoi la requête après que l'utilisateur se soit connecté
        $client->request('GET', '/api/books');

        //Vérifie que la requête Http renvoi quelque chose
        self::assertResponseIsSuccessful();

        //Récupère la réponse et vérifie son format
        self::assertJson($client->getResponse()->getContent());
    }

    //Vérifie que la requête sur l'url API sans authentification ne fonctionne pas
    public function testRouteIsRestricted() {
        $client = self::callClient('GET','/api/books');

        //Vérifie que la requête Http renvoi une erreur d'authorisation liée au token JWT
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    //Fonction utilitaire pour raccourcir le code des tests
    private function callClient($method, $url) {
        $client = static::createClient();
        $client->request($method, $url);
        return $client;
    }
}
