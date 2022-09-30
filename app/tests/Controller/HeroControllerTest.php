<?php

use App\Entity\Hero;
use App\Repository\HeroRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HeroControllerTest extends WebTestCase
{
    //Check api route sends back Json data
    public function testGetAllHeroesIsJson(): void
    {
        $client = $this->callClient('GET', '/api/heroes');

        //Checking that http request does send back data
        self::assertResponseIsSuccessful();
        //Retrieves response and checks content is in Json
        self::assertJson($client->getResponse()->getContent());
    }

    //Check api route sends back Json data
    public function testGetOneHeroIsJson(): void
    {
        $client = static::createClient();
        $heroRepository = static::getContainer()->get(HeroRepository::class);
        //Test to be updated to retrieve a random id number from database ///TO DO
        $hero = $heroRepository->findOneBy(['id' => 15]);
        $heroId = $hero->getId();
        $client->request('GET', '/api/heroes/'.$heroId);

        //Checking that http request does send back data
        self::assertResponseIsSuccessful();
        //Retrieves response and checks content is in Json
        self::assertJson($client->getResponse()->getContent());
    }

    //Toolkit function to shorten test code
    private function callClient($method, $url):KernelBrowser {
        $client = static::createClient();
        $client->request($method, $url);
        return $client;
    }
}
