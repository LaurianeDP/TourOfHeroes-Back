<?php

namespace App\Tests\Controller;

use App\Entity\Hero;
use App\Entity\Power;
use App\Repository\HeroRepository;
use App\Repository\PowerRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class HeroControllerTest extends WebTestCase
{
   protected function setUp(): void
   {
       parent::setUp();
       /** @var EntityManager $manager */
       $manager = static::getContainer()->get(EntityManagerInterface::class);
       $manager->beginTransaction();
   }

   protected function tearDown(): void
   {
       /** @var EntityManager $manager */
       $manager = static::getContainer()->get(EntityManagerInterface::class);
       $manager->rollback();
       parent::tearDown();

   }


    //Checks api route sends back Json data
    public function testGetAllHeroesIsJson(): void
    {
        $client = $this->callClient('GET', '/api/heroes');

        //Checking that http request does send back data
        self::assertResponseIsSuccessful();
        //Retrieves response and checks content is in Json
        self::assertJson($client->getResponse()->getContent());
    }

    //Checks api route sends back Json data
    public function testGetOneHeroIsJson(): void
    {
        $client = static::createClientBetter();
        $heroRepository = static::getContainer()->get(HeroRepository::class);
        //Test retrieves a random id number from database
        $allHeroes = $heroRepository->findAll();
        $randomHero = $allHeroes[array_rand($allHeroes)];
        $heroId = $randomHero->getId();
        $client->request('GET', '/api/heroes/'.$heroId);

        //Checking that http request does send back data
        self::assertResponseIsSuccessful();
        //Retrieves response and checks content is in Json
        self::assertJson($client->getResponse()->getContent());
    }

    //Checks that api route sends back a successful response and that a new object is
    // created in the database
    public function testCreationWhenValid() {
        $client = static::createClientBetter();
        //Count the number of items in the hero database
        $heroRepository = static::getContainer()->get(HeroRepository::class);
        $allHeroes = $heroRepository->findAll();
        $numberOfHeroesBefore = count($allHeroes);

        //Get a random power from the database to assign it to the new hero
        $powerRepository = static::getContainer()->get(PowerRepository::class);
        $allPowers = $powerRepository->findAll();
        $randomPower = $allPowers[array_rand($allPowers)];

        $randomPower = [
            'id' => $randomPower->getId(),
            'name' => $randomPower->getName()
            ];

        $json = json_encode([
            'name' => 'New super Hero test',
            'power' => $randomPower,
            'alterEgo' => 'New hero\'s real name'
        ]);
        $client->request('POST', '/api/hero', content: $json
        );
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        //Count number of heroes in the database after hero creation, expects an
        // addition of only one
        $allHeroes = $heroRepository->findAll();
        $numberOfHeroesAfter = count($allHeroes);
        self::assertEquals(($numberOfHeroesBefore + 1), $numberOfHeroesAfter);
    }

    //Checks that the database entry with the associated id is deleted, and that the
    // http request sends back an adequate response
    public function testDeleteHeroWithId() {
        $client = static::createClientBetter();
        $heroRepository = static::getContainer()->get(HeroRepository::class);
        //Test retrieves a random id number from database
        $allHeroes = $heroRepository->findAll();

        //Counts all heroes for final check;
        $numberOfHeroesBefore = count($allHeroes);

        $randomHero = $allHeroes[array_rand($allHeroes)];
        $heroId = $randomHero->getId();
        //Test deletes the hero from database
        $client->request('DELETE', '/api/heroes/'.$heroId);
        //Checks that http sends back code saying an item was deleted and there is
        // now no content associated with that id
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        //Checks that the number of heroes in the database is the same minus one
        $allHeroes = $heroRepository->findAll();
        $numberOfHeroesAfter = count($allHeroes);
        self::assertEquals(($numberOfHeroesBefore - 1), $numberOfHeroesAfter);
    }

    //Checks that an item is updated and that the http request sends back an
    // adequate response
    public function testUpdateWhenValid() {
        $client = static::createClientBetter();
        //Count the number of items in the hero database
        $powerRepository = static::getContainer()->get(PowerRepository::class);
        $heroRepository = static::getContainer()->get(HeroRepository::class);
        $allHeroes = $heroRepository->findAll();
        $randomHero = $allHeroes[array_rand($allHeroes)];
        $heroId = $randomHero->getId();
        $allPowers = $powerRepository->findAll();
        $randomPower = $allPowers[array_rand($allPowers)];

        $randomPowerArr = [
            'id' => $randomPower->getId(),
            'name' => $randomPower->getName()
        ];

        $json = json_encode([
            'name' => 'NewName',
            'power' => $randomPowerArr
        ]);
        $client->request('PUT', '/api/heroes/'.$heroId, content: $json
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $updatedHero= $heroRepository->find($heroId);
        //Checks that the updated hero name is the right one
        self::assertEquals('NewName', $updatedHero->getName());
        self::assertEquals($randomPower, $updatedHero->getPower());
    }

    //Toolkit function to shorten test code
    private function callClient($method, $url):KernelBrowser {
        $client = static::createClientBetter();
        $client->request($method, $url);
        return $client;
    }

    protected static function createClientBetter(array $options = [], array $server = []): KernelBrowser {
        if(static::$booted === false)
            static::bootKernel($options);

        $kernel = static::$kernel;
        $newClient = $kernel->getContainer()->get('test.client');
        $newClient->setServerParameters($server);

        $class = new \ReflectionClass(WebTestCase::class);
        $method = $class->getMethod('getClient');
        $method->setAccessible(true);

        $method->invokeArgs(null, [$newClient]);

        return $newClient;
    }
}
