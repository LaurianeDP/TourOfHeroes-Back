<?php

use App\Entity\Hero;
use App\Repository\HeroRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PowerControllerTest extends WebTestCase
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

    //Checks that api route sends back Json data
    public function testGetAllPowersIsJson(): void
    {
        $client = $this->callClient('GET', '/api/powers');

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
