<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testGetAllBooks(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/books');

        $this->assertResponseIsSuccessful();

        $response = $client->getResponse();
        $data = $response->getContent();

        $this->assertStringContainsString("Symfony and PHP", $data);
    }
}
