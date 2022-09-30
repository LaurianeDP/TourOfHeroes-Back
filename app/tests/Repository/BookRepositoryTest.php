<?php

namespace App\Tests;

use App\Factory\BookFactory;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BookRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private BookRepository $bookRepository;
    protected function setUp(): void
    {
        //START THE SYMFONY KERNEL
        $kernel = self::bootKernel();
        $this->assertSame('test', $kernel->getEnvironment());
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //ACCESS THE SERVICE CONTAINER
        $container = static::getContainer();

        //GET REPOSITORY INSIDE THE CONTAINER
        $this->bookRepository = $container->get(BookRepository::class);
    }

    public function tearDown(): void
    {
            parent::tearDown();
            $this->entityManager->close();
    }

    public function testCreateBook():void {
        $entity = BookFactory::createBook("test title", "test coverText");
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->assertNotNull($entity->getId());

        $byId = $this->bookRepository->findOneBy(["id" => $entity->getId()]);
        $this->assertEquals("test title", $byId->getTitle());
        $this->assertEquals("test coverText", $byId->getCoverText());
    }
}
